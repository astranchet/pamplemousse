<?php
namespace Pamplemousse\Photos;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextAreaType;

use Pamplemousse\Photos\Entity\Photo;

class Controller
{

    /**
     * @param  Application $app
     * @param  Request     $request
     * @param  Photo       $photo
     * @return Response
     */
    public function photoAction(Application $app, Request $request, Photo $photo)
    {
        $form = $app['form.factory']->createBuilder(FormType::class)
            ->add('item_id', HiddenType::class)
            ->add('name', TextType::class, [ 'label' => "Nom" ])
            ->add('comment', TextAreaType::class, [ 'label' => "Commentaire" ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $app['comments']->add($photo, $form->getData());
            return $app->redirect($app['url_generator']->generate('photo', [ 'photo' => $photo->id ]));
        }

        return $app['twig']->render('photo.twig', [
            'photo' => $photo,
            'comments' => $app['comments']->getComments($photo->id),
            'form' => $form->createView()
        ]);
    }

    /**
     * @param  Application $app
     * @param  Request     $request
     * @param  Photo       $photo
     * @param  int         $width
     * @param  int         $height
     * @param  string      $algorithm
     * @return Response
     */
    public function thumbnailAction(Application $app, Request $request, $photo, $width, $height = null, $algorithm = null)
    {
        if (!$photo) {
            return $app->abort(404);
        }

        $thumbnailPath = $app['photos']->getThumbnail($photo, $width, $height, $algorithm);
        $stream = function () use ($thumbnailPath) {
            $resource = imagecreatefromjpeg($thumbnailPath);
            imagejpeg($resource);
        };

        return $app->stream($stream, 200, [
            'Content-type'        => 'image/jpeg',
            'Content-Disposition' => sprintf('filename="%s"', $photo->filename),
        ]);
    }

    /**
     * @param  Application $app
     * @param  Request     $request
     * @param  Photo       $photo
     * @param  int         $size
     * @param  string      $algorithm
     * @return Response
     */
    public function thumbnailSquareAction(Application $app, Request $request, $photo, $size, $algorithm = null)
    {
        return $this->thumbnailAction($app, $request, $photo, $size, $size, $algorithm);
    }
}
