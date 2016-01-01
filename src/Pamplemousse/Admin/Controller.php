<?php
namespace Pamplemousse\Admin;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

use Upload\Storage\FileSystem;
use Upload\File;
use Upload\Validation\Mimetype;
use Upload\Validation\Size;

class Controller
{

    /**
     * @param  Application $app
     * @param  Request     $request
     * @return Response
     */
    public function indexAction(Application $app, Request $request)
    {
        return $app['twig']->render('admin/index.twig', [
            'photos' => $app['photos']->getPhotos()
        ]);
    }

    /**
     * @param  Application $app
     * @param  Request     $request
     * @return Response
     */
    public function editAction(Application $app, Request $request)
    {
        // $photos = $app['photos']->getPhotosByIds($request->get('ids'));

        $data = [
            'description' => [1 => "Ola", 6 => "Hello", "truc" => "World", "9" => null, '20' => "!"]
        ];
        $builder = $app['form.factory']->createBuilder(FormType::class, $data);
        $builder->add('description', CollectionType::class, array(
            'entry_type' => TextType::class,
            'entry_options' => array(
                'required' => false
            )
        ));

        $form = $builder->getForm();

        return $app['twig']->render('admin/edit.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param  Application $app
     * @param  Request     $request
     * @return Response
     */
    public function fileUploadAction(Application $app, Request $request)
    {
        $webDirectory = __DIR__.'/../../../web';
        $storage = new FileSystem($webDirectory . $app['config']['upload_dir']);
        $file = new File('file', $storage);

        // Validate file upload
        $file->addValidations(array(
            new Mimetype(array('image/png', 'image/gif', 'image/jpeg')),
            new Size('5M')
        ));

        // Access data about the file that has been uploaded
        $data = array(
            'name'       => $file->getNameWithExtension(),
            'extension'  => $file->getExtension(),
            'mime'       => $file->getMimetype(),
            'size'       => $file->getSize(),
            'md5'        => $file->getMd5(),
            'dimensions' => $file->getDimensions()
        );

        $app['monolog']->addDebug(sprintf("File uploaded: %s", json_encode($data)));

        // Upload file to server
        try {
            $file->setName($app['slug']->slugify($file->getName()));
            $file->upload();
        } catch (\Exception $exception) {
            $errorMessage = join('<br />', $file->getErrors());
            $app['monolog']->addError(sprintf("Error during file upload: %s", $errorMessage));
            return new Response(sprintf("Erreur : %s", $errorMessage), 400);
        }

        // Save file to db
        try {
            $filepath = sprintf("%s%s.%s",$app['config']['upload_dir'], $file->getName(), $file->getExtension());
            $photoId = $app['photos']->add($filepath);
        } catch (\Exception $exception) {
            $app['monolog']->addError(sprintf("Error during file upload: %s", $exception->getMessage()));
            return new Response(sprintf("Erreur : %s", $exception->getMessage()), 400);
        }

        return new Response($photoId);
    }

}
