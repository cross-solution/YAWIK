<?php


namespace Auth\Listener;

use Zend\Mvc\View\Http\ExceptionStrategy;
use Zend\View\Model\ViewModel;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Mvc\MvcEvent;
use Auth\Exception\UnauthorizedAccessException;


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
        if (!$exception instanceOf UnauthorizedAccessException) {
            return;
        }
    
        $message = $exception->getMessage();
        $model = new ViewModel(array(
            'message'            => empty($message)
                                    ? 'You are not permitted to access this resource.'
                                    : $message,
            'exception'          => $e->getParam('exception'),
            'display_exceptions' => $this->displayExceptions(),
        ));

        $model->setTemplate($this->getExceptionTemplate());
        $e->setResult($model);

        $response = $e->getResponse();
        if (!$response) {
            $response = new HttpResponse();
            $response->setStatusCode(403);
            $e->setResponse($response);
        } else {
            $statusCode = $response->getStatusCode();
            if ($statusCode === 200) {
                $response->setStatusCode(403);
            }
        }

    }
    
}