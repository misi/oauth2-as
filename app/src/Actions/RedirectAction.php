<?php
namespace OAuth2Server\Actions;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Stream;

use Psr\Log\LoggerInterface;

use Slim\Views\Twig;


final class RedirectAction
{
    private $logger;

    private $view;


    public function __construct(LoggerInterface $logger, TWIG $view)
    {
        $this->logger = $logger;

        $this->view = $view;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $this->logger->info("Redirect action dispatched");

        $code=$request->getQueryParams()['code'];
        if ( !isset($code) ) {
            $this->view->render($response, 'r edirect.twig',
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
