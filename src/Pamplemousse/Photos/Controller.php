<?php
namespace Pamplemousse\Photos;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Controller
{

    /**
     * @param  Application $app
     * @param  Request     $request
     * @param  string      $filename
     * @param  string      $dimensions
     * @return Response
     */
    public function thumbnailAction(Application $app, Request $request, $filename, $dimensions)
    {
        // TODO : generate thumbnail
        return new Response("thumbnail");
    }

}
