<?php


namespace Auth\Listener;

use Laminas\Mvc\View\Http\ExceptionStrategy;
use Laminas\View\Model\ViewModel;
use Laminas\Mvc\MvcEvent;
use Auth\Exception\UnauthorizedAccessException;
use Laminas\Http\PhpEnvironment\Response;
use Auth\Exception\UnauthorizedImageAccessException;

class UnauthorizedAccessListener extends ExceptionStrategy
{

    /**
     * Create an exception view model, and set the HTTP status code
     *
     * @todo   dispatch.error does not halt dispatch unless a response is
     *         returned. As such, we likely need to trigger rendering as a low
     *         priority dispatch.error event (or goto a render event) to ensure
     *         rendering occurs, and that munging of view models occurs when
     *         expected.
     * @param  MvcEvent $e
     * @return void
     */
    public function prepareExceptionViewModel(MvcEvent $e)
    {
        // Do nothing if no error in the event
        $error = $e->getError();
        if (empty($error)) {
            return;
        }

        // Do nothing if the result is a response object
        $result = $e->getResult();
        if ($result instanceof Response) {
            return;
        }

        // Do nothing if there is no exception or the exception is not
        // an UnauthorizedAccessException
        $exception = $e->getParam('exception');
        if (!$exception instanceof UnauthorizedAccessException) {
            return;
        }

        $response = $e->getResponse();
        if (!$response) {
            $response = new Response();
            $e->setResponse($response);
        }

        /*
         * Return an image, if an image was requested.
         */
        if ($exception instanceof UnauthorizedImageAccessException) {
            $image = __DIR__ . '/../../../../../public/images/unauthorized-access.png';
            $response->setStatusCode(Response::STATUS_CODE_403)
                     ->setContent(file_get_contents($image))
                     ->getHeaders()
                     ->addHeaderLine('Content-Type', 'image/png');
            $e->stopPropagation();
            $response->sendHeaders();
            //echo file_get_contents($image);


            //$response->stopped = true;
            return $response;
        }

        $application = $e->getApplication();
		$auth = $application->getServiceManager()->get('AuthenticationService');

        if (!$auth->hasIdentity()) {
            $request = $e->getRequest();
            $loginPrefill = $request->getQuery()->get('login');
            $ref = $request->getRequestUri();
            $query = [];
            if ($loginPrefill) {
                $ref = preg_replace('~(?:\?|&)login=' . $loginPrefill . '~', '', $ref);
                $query['login'] = $loginPrefill;
            }
            $ref = preg_replace('~^' . preg_quote($e->getRouter()->getBaseUrl()) . '~', '', $ref);
            $ref = urlencode($ref);
            $query['ref'] = $ref;
            $url = $e->getRouter()->assemble([], ['name' => 'lang/auth', 'query' => $query]);
            if ($request->isXMLHttpRequest()) {
                $response->setStatusCode(Response::STATUS_CODE_401);
                $response->getHeaders()->addHeaderLine(
                    'X-YAWIK-Login-Url', $url
                );
            } else {
                $response->setStatusCode(Response::STATUS_CODE_303);
                $response->getHeaders()->addHeaderLine('Location', $url);
            }

            $e->stopPropagation();
            return $response;
        }
        $message = $exception->getMessage();
        $model = new ViewModel(
            array(
            'message'            => empty($message)
                                    ? /*@translate*/ 'You are not permitted to access this resource.'
                                    : $message,
            'exception'          => $e->getParam('exception'),
            'display_exceptions' => $this->displayExceptions(),
            )
        );

        $model->setTemplate($this->getExceptionTemplate());
        $e->setResult($model);

       // $statusCode = $response->getStatusCode();
       // if ($statusCode === 200) {
            $response->setStatusCode(Response::STATUS_CODE_403);
       // }
    }
}
