<?php
namespace OAuth2Server\Actions;

use Interop\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Stream;

final class RedirectAction
{
    private $logger;

    private $view;


    public function __construct(ContainerInterface $c)
    {
        $this->logger = $logger;

        $this->view = $view;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response)
    {
        $this->logger->info("Redirect action dispatched");

        $code=$request->getQueryParams()['code'];
        if ( isset($code) ) {
            $this->view->render($response, 'redirect.twig',
              [
                'code' => $code,
		          ]
            );
            return $response;
        } else {
            return $response->withStatus(500);
        }
    }
}
