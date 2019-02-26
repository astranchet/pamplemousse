<?php

namespace Pamplemousse\Photos;

use Symfony\Component\HttpFoundation\Request;

use PHPImageWorkshop\ImageWorkshop;

class Service
{
    const TABLE_NAME = 'pamplemousse__item';

    const 
        CROP_CENTER   = 'Center',
        CROP_ENTROPY  = 'Entropy',
        CROP_BALANCED = 'Balanced';

    const 
        BY_MONTH = 'month',
        BY_YEAR  = 'year';

    protected $app;
    protected $config;
    protected $conn;

    public function __construct($app)
    {
        $this->app = $app;
        $this->config = $app['config'];
        $this->conn = $app['db'];
    }

    public static function getCropAlgorithms()
    {
        return [
            self::CROP_CENTER,
            self::CROP_ENTROPY,
            self::CROP_BALANCED,
        ];
    }

    public function add($filename)
    {
        $this->conn->insert(self::TABLE_NAME, $this->getDataFromFile($filename));
        return $this->getPhoto($this->conn->lastInsertId());
    }

    protected function getDataFromFile($filename)
    {
        $filepath = $this->getUploadDir() . $filename;
        $image = $this->app['imagine']->open($filepath);
        list($width, $height) = getimagesize($filepath);
        $metadata = $image->metadata();

        return [
            'path' => $this->app['config']['upload_dir'] . $filename,
            'date_taken' => $metadata["exif.DateTimeOriginal"],
            'width' => $width,
            'height' => $height,
            'crop_algorithm' => self::CROP_ENTROPY
        ];
    }

    public function getAll()
    {
        $items = $this->conn->fetchAll(sprintf('SELECT * FROM %s WHERE type = ? ORDER BY date_taken DESC', self::TABLE_NAME), array('picture'));

        foreach ($items as $id => $item) {
            yield new Entity\Photo($this->app, $item);
        }
    }

    public function getLast($limit = 50, $from = null)
    {
        $stmt = $this->conn->prepare(sprintf('SELECT * FROM %s WHERE type = :type AND date_taken < :date_from ORDER BY date_taken DESC LIMIT %d', 
            self::TABLE_NAME, $limit));
        $stmt->bindValue('type', 'picture');
        $stmt->bindValue('date_from', new \DateTime($from), "datetime");
        $stmt->execute();
        $items = $stmt->fetchAll();

        $photos = [];
        foreach ($items as $id => $item) {
            $photos[] = new Entity\Photo($this->app, $item);
        }
        return $photos;
    }

    public function getWithTag($tag, $limit = 50, $from = null)
    {
        $stmt = $this->conn->prepare(sprintf('SELECT * FROM %s LEFT JOIN %s ON %s.id = %s.item_id 
            WHERE type = :type AND date_taken < :date_from AND tag = :tag ORDER BY date_taken DESC LIMIT %d', 
            self::TABLE_NAME, \Pamplemousse\Tags\Service::TABLE_NAME,
            self::TABLE_NAME, \Pamplemousse\Tags\Service::TABLE_NAME,
            $limit));
        $stmt->bindValue('type', 'picture');
        $stmt->bindValue('date_from', new \DateTime($from), "datetime");
        $stmt->bindValue('tag', $tag);
        $stmt->execute();
        $items = $stmt->fetchAll();

        $photos = [];
        foreach ($items as $id => $item) {
            $photos[] = new Entity\Photo($this->app, $item);
        }
        return $photos;
    }

    public function getForKids($kids, $limit = 50, $from = null)
    {
        $qb = $this->conn->createQueryBuilder()
            ->select('*')
            ->from(self::TABLE_NAME, 'items');
        foreach ($kids as $id => $kid) {
            $qb->join('items', \Pamplemousse\Kids\Service::TABLE_NAME, 't'.$id, 
                sprintf('items.id = %s.item_id AND %s.kid = "%s"', 't'.$id, 't'.$id, $kid));
        }
        $qb->where('type = "picture"')
            ->orderBy('date_taken', 'DESC')
            ->setMaxResults($limit);
        if(!is_null($from)) {
            $qb->andWhere('date_taken < :date_from')
                ->setParameter(':date_from', new \DateTime($from), "datetime");
        }
        // echo $qb->getSQL(); die;
        
        $stmt = $qb->execute();
        $items = $stmt->fetchAll();

        $photos = [];
        foreach ($items as $item) {
            $photos[$item['id']] = new Entity\Photo($this->app, $item);
        }

        return $photos;
    }

    public function getForDate($month, $year)
    {
        $items = $this->conn->fetchAll(sprintf("SELECT * FROM %s HAVING MONTH(date_taken) = ? AND YEAR(date_taken) = ? ORDER BY date_taken ASC", 
            self::TABLE_NAME), [$month, $year]);

        $photos = [];
        foreach ($items as $id => $item) {
            $photos[] = new Entity\Photo($this->app, $item);
        }
        return $photos;
    }

    public function getPhotosByIds($ids, Request $request = null)
    {
        if (is_null($ids)) {
            $ids = $request->get('ids');
        }

        $statement = $this->conn->executeQuery(sprintf('SELECT * FROM %s WHERE type = "picture" AND id IN (?) ORDER BY date_taken DESC', self::TABLE_NAME),
            [$ids],
            [\Doctrine\DBAL\Connection::PARAM_INT_ARRAY]);
        $items = $statement->fetchAll();

        $photos = [];
        foreach ($items as $id => $item) {
            $photos[$item['id']] = new Entity\Photo($this->app, $item);
        }

        return $photos;
    }

    public function getPhoto($id)
    {
        $item = $this->conn->fetchAssoc(sprintf('SELECT * FROM %s WHERE id = ?', self::TABLE_NAME), array($id));
        if ($item) {
            return new Entity\Photo($this->app, $item);
        }
        return false;
    }

    public function getNextPhoto($photo)
    {
        $item = $this->conn->fetchAssoc(sprintf('SELECT * FROM %s WHERE date_taken > ? ORDER BY date_taken LIMIT 1', self::TABLE_NAME), array($photo->date_taken));
        if ($item) {
            return new Entity\Photo($this->app, $item);
        }
        return false;
    }

    public function getPreviousPhoto($photo)
    {
        $item = $this->conn->fetchAssoc(sprintf('SELECT * FROM %s WHERE date_taken < ? ORDER BY date_taken DESC LIMIT 1', self::TABLE_NAME), array($photo->date_taken));
        if ($item) {
            return new Entity\Photo($this->app, $item);
        }
        return false;
    }

    public function getAggregatedDates($aggregateBy = self::BY_MONTH)
    {
        $items = $this->conn->fetchAll("SELECT distinct date_format(date_taken, '%Y-%m') as month FROM " .
            self::TABLE_NAME . " WHERE date_taken IS NOT NULL ORDER BY month");

        if ($aggregateBy == self::BY_YEAR) {
            $dates = [];
            foreach ($items as $item) {
                list($year, $month) = explode("-", $item['month']);
                if (!isset($dates[$year])) {
                    $dates[$year] = [];
                }
                $dates[$year][] = $month;
            }
        } else {
            $dates = array_column($items, 'month');
        }

        return $dates;
    }

    public function getNextMonth($year, $month)
    {
        $date = sprintf("%s-%s", $year, $month);
        $dates = self::getAggregatedDates();
        $index = array_search($date, $dates);

        if ($index < sizeof($dates)-1) {
            return $dates[++$index];
        } else {
            return null;
        }
    }

    public function getPreviousMonth($year, $month)
    {
        $date = sprintf("%s-%s", $year, $month);
        $dates = self::getAggregatedDates();
        $index = array_search($date, $dates);

        if ($index > 0) {
            return $dates[--$index];
        } else {
            return null;
        }
    }

    public function findFromFilename($filename)
    {
        $item = $this->conn->fetchAssoc(sprintf('SELECT * FROM %s WHERE path LIKE ?', self::TABLE_NAME), array("%/".$filename));
        if ($item) {
            return new Entity\Photo($this->app, $item);
        }
        return false;
    }

    public function update($photo)
    {
        $data = [
            'description' => $photo->description,
            'is_favorite' => $photo->is_favorite,
            'crop_algorithm' => $photo->crop_algorithm,
            'date_taken' => $photo->date_taken
        ];

        $this->app['tags']->delete($photo);
        $this->app['tags']->add($photo);

        $this->app['kids']->delete($photo);
        $this->app['kids']->add($photo);

        return $this->conn->update(self::TABLE_NAME, $data, array('id' => $photo->id));
    }

    public function delete($photo)
    {
        if ($photo->exists()) {
            unlink($photo->getImagePath());
        }
        $thumbnails = $photo->getThumbnails();
        foreach ($thumbnails as $thumbnail) {
            unlink($thumbnail);
        }
        return $this->conn->delete(self::TABLE_NAME, array('id' => $photo->id));
    }

    /**
     * Retrieve thumbnail (or generate it) for given photo and size
     * 
     * @param  Photo $photo
     * @param  int   $width
     * @param  int   $height
     * @param  string $cropAlgorithm
     * @return string The thumbnail path
     */
    public function getThumbnail($photo, $width, $height, $cropAlgorithm = null)
    {
        if (is_null($cropAlgorithm)) {
            $cropAlgorithm = $photo->crop_algorithm;
        }

        $thumbnailPath = $this->getThumbnailPath($photo, $width, $height, $cropAlgorithm);

        if (file_exists($thumbnailPath)) {
            return $thumbnailPath;
        }

        return $this->generateThumbnail($photo, $width, $height, $cropAlgorithm);
    }

    public function getThumbnailPath($photo, $width, $height, $cropAlgorithm = null)
    {
        $thumbnailDir = $this->getThumbnailDir($width, $height);
        switch ($cropAlgorithm) {
            case self::CROP_BALANCED:
                $filename = $cropAlgorithm . '-' . $photo->filename;
                break;
            case self::CROP_ENTROPY:
                $filename = $cropAlgorithm . '-' . $photo->filename;
                break;
            case self::CROP_CENTER:
            default:
                $filename = $photo->filename;
                break;
        }

        return $thumbnailDir . $filename;
    }

    protected function generateThumbnail($photo, $width, $height, $cropAlgorithm = null)
    {
        $thumbnailDir = $this->getThumbnailDir($width, $height);
        if (!is_dir($thumbnailDir)) {
            mkdir($thumbnailDir, 0755, true);
        }

        if ($width == $height) {
            return $this->generateSquareThumbnail($photo, $width, $height, $cropAlgorithm);
        }

        $thumbnailPath = $this->getThumbnailPath($photo, $width, $height, $cropAlgorithm);
        $thumbnailDir = dirname($thumbnailPath);
        $thumbnailName = basename($thumbnailPath);

        $layer = ImageWorkshop::initFromPath($photo->getImagePath());
        $layer->resizeInPixel($width, $height, true);

        $createFolders = true;
        $backgroundColor = null;
        $imageQuality = 95;
        $layer->save($thumbnailDir, $thumbnailName, $createFolders, $backgroundColor, $imageQuality);

        $this->app['monolog']->addDebug(sprintf("Thumbnail generated: %s", $thumbnailPath));

        return $thumbnailPath;
    }

    public function generateThumbnails($photo)
    {
        foreach ($this->app['config']['thumbnails']['size'] as $size) {
            list($width, $height) = preg_split('/x/', $size);
            $this->generateThumbnail($photo, $width, $height);
        }
    }

    protected function generateSquareThumbnail($photo, $width, $height, $cropAlgorithm = null)
    {
        $thumbnailPath = $this->getThumbnailPath($photo, $width, $height, $cropAlgorithm);

        switch ($cropAlgorithm) {
            case self::CROP_BALANCED:
                $cropper = new \stojg\crop\CropBalanced();
                break;
            case self::CROP_ENTROPY:
                $cropper = new \stojg\crop\CropEntropy();
                break;
            case self::CROP_CENTER:
            default:
                $cropper = new \stojg\crop\CropCenter();
                break;
        }

        $cropper->setImage(new \Imagick($photo->getImagePath()));
        $croppedImage = $cropper->resizeAndCrop($width, $height);
        $croppedImage->writeimage($thumbnailPath);

        $this->app['monolog']->addDebug(sprintf("Thumbnail generated: %s", $thumbnailPath));

        return $thumbnailPath;
    }

    public function getUploadDir()
    {
        return __DIR__.'/../../../web' . $this->config['upload_dir'] . DIRECTORY_SEPARATOR;
    }

    public function getThumbnailDir($width = null, $height = null)
    {
        $thumbnailDir = __DIR__.'/../../../web' . $this->config['thumbnails']['dir'];
        if (!is_null($width) || !is_null($height)) {
            $thumbnailDir .= $width . 'x' . $height . DIRECTORY_SEPARATOR;
        }
        return $thumbnailDir;
    }

}
