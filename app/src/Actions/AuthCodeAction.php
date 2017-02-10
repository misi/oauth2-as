<?php
namespace OAuth2Server\Actions;


use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use OAuth2Server\Entities\UserEntity;
use League\OAuth2\Server\ClientEntity;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Stream;

use Psr\Log\LoggerInterface;

use Slim\Views\Twig;

final class AuthCodeAction
{
    private $logger;

    private $authserver;

    private $userrepository;

    private $view;

    public function __construct(UserRepositoryInterface $userrepository, AuthorizationServer $authserver, LoggerInterface $logger, TWIG view)
    {
        $this->logger = $logger;
        $this->authserver = $authserver;
        $this->userrepository = $userrepository;
        $this->view = $view;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $this->logger->info("auth action dispatched");
        try {

            // If user is not authenticated
            if (!isset($request->getServerParams('PHP_AUTH_USER'))) {
                $response->withHeader('WWW-Authenticate: Basic realm="OAUTH"');
                $response->withStatus(401);
            }


            // Validate the HTTP request and return an AuthorizationRequest object.
            // The auth request object can be serialized into a user's session
            $authRequest = $this->authserver->validateAuthorizationRequest($request);

            if ($request->getQueryStringParameter('response_type')==code) {
                $user=$this->userrepository->getUserEntityByUserCredentials(
                    $request->getServerParams('PHP_AUTH_USER'),
                    $request->getServerParams('PHP_AUTH_PW'),
                    $authRequest->getGrantTypeId(),
                    $authRequest->getClient()
                );
            }

            // Once the user has logged in set the user on the AuthorizationRequest
            $authRequest->setUser($user);

            $this->view->render($response, 'consent.twig', [
					'client' => $authRequest->getClient()->getName(), 
					'description' => $authRequest->getClient()->getDescription();
				]);

            // Once the user has approved or denied the client update the status
            // (true = approved, false = denied)
            $authRequest->setAuthorizationApproved(true);

            // Return the HTTP redirect response
            return $this->authserver->completeAuthorizationRequest($authRequest, $response);

        } catch (OAuthServerException $exception) {
            return $exception->generateHttpResponse($response);
        } catch (\Exception $exception) {
            $body = new Stream('php://temp', 'r+');
            $body->write($exception->getMessage());

            return $response->withStatus(500)->withBody($body);
        }
    }
}
