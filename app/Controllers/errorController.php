<?php
declare(strict_types=1);

/**
 * Controller to handle error page.
 */
class FreshRSS_error_Controller extends FreshRSS_ActionController {
	/**
	 * This action is the default one for the controller.
	 *
	 * It is called by Minz_Error::error() method.
	 *
	 * Parameters are passed by Minz_Session to have a proper url:
	 *   - error_code (default: 404)
	 *   - error_logs (default: [])
	 */
	public function indexAction(): void {
		$code_int = Minz_Session::paramInt('error_code') ?: FreshRSS_HttpResponse::HTTP_404_NOT_FOUND->value;
		/** @var array<string> */
		$error_logs = Minz_Session::paramArray('error_logs');
		Minz_Session::_params([
			'error_code' => false,
			'error_logs' => false,
		]);

		switch ($code_int) {
			case FreshRSS_HttpResponse::HTTP_200_OK->value:
				header(FreshRSS_HttpResponse::description(FreshRSS_HttpResponse::HTTP_200_OK));
				break;
			case FreshRSS_HttpResponse::HTTP_400_BAD_REQUEST->value:
				header(FreshRSS_HttpResponse::description(FreshRSS_HttpResponse::HTTP_400_BAD_REQUEST));
				$this->view->code = 'Error 400 - Bad Request';
				$this->view->errorMessage = '';
				break;
			case FreshRSS_HttpResponse::HTTP_403_FORBIDDEN->value:
				header(FreshRSS_HttpResponse::description(FreshRSS_HttpResponse::HTTP_403_FORBIDDEN));
				$this->view->code = 'Error 403 - Forbidden';
				$this->view->errorMessage = _t('feedback.access.denied');
				break;
			case FreshRSS_HttpResponse::HTTP_404_NOT_FOUND->value:
				header(FreshRSS_HttpResponse::description(FreshRSS_HttpResponse::HTTP_404_NOT_FOUND));
				$this->view->code = 'Error 404 - Not found';
				$this->view->errorMessage = _t('feedback.access.not_found');
				break;
			case FreshRSS_HttpResponse::HTTP_405_METHOD_NOT_ALLOWED->value:
				header(FreshRSS_HttpResponse::description(FreshRSS_HttpResponse::HTTP_405_METHOD_NOT_ALLOWED));
				$this->view->code = 'Error 405 - Method Not Allowed';
				$this->view->errorMessage = '';
				break;
			case FreshRSS_HttpResponse::HTTP_503_SERVICE_UNAVAILABLE->value:
				header(FreshRSS_HttpResponse::description(FreshRSS_HttpResponse::HTTP_503_SERVICE_UNAVAILABLE));
				$this->view->code = 'Error 503 - Service Unavailable';
				$this->view->errorMessage = 'Error 503 - Service Unavailable';
				break;
			case FreshRSS_HttpResponse::HTTP_500_INTERNAL_SERVER_ERROR->value:
			default:
				header(FreshRSS_HttpResponse::description(FreshRSS_HttpResponse::HTTP_500_INTERNAL_SERVER_ERROR));
				$this->view->code = 'Error 500 - Internal Server Error';
				$this->view->errorMessage = 'Error 500 - Internal Server Error';
				break;
		}

		$error_message = trim(implode('', $error_logs));
		if ($error_message !== '') {
			$this->view->errorMessage = $error_message;
		}

		FreshRSS_View::prependTitle($this->view->code . ' · ');
	}
}
