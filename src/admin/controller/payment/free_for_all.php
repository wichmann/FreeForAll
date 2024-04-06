<?php
namespace Opencart\Admin\Controller\Extension\FreeForAll\Payment;

/**
 * Class FreeForAll
 *
 * @package Opencart\Admin\Controller\Extension\FreeForAll\Payment
 */
class FreeForAll extends \Opencart\System\Engine\Controller
{
	/**
	 * @return void
	 */
	public function index(): void
	{
		$this->load->language('extension/free_for_all/payment/free_for_all');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment')
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/free_for_all/payment/free_for_all', 'user_token=' . $this->session->data['user_token'])
		];

		$data['save'] = $this->url->link('extension/free_for_all/payment/free_for_all.save', 'user_token=' . $this->session->data['user_token']);
		$data['back'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment');

		$data['payment_free_for_all_order_status_id'] = $this->config->get('payment_free_for_all_order_status_id');

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		$data['payment_free_for_all_status'] = $this->config->get('payment_free_for_all_status');
		$data['payment_free_for_all_sort_order'] = $this->config->get('payment_free_for_all_sort_order');

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/free_for_all/payment/free_for_all', $data));
	}

	/**
	 * @return void
	 */
	public function save(): void
	{
		$this->load->language('extension/free_for_all/payment/free_for_all');

		$json = [];

		if (!$this->user->hasPermission('modify', 'extension/free_for_all/payment/free_for_all')) {
			$json['error'] = $this->language->get('error_permission');
		}

		if (!$json) {
			$this->load->model('setting/setting');

			$this->model_setting_setting->editSetting('payment_free_for_all', $this->request->post);

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	/*public function install(): void {
		   if ($this->user->hasPermission('modify', 'extension/payment')) {
			   $this->load->model('extension/oc_payment_example/payment/credit_card');

			   $this->model_extension_oc_payment_example_payment_credit_card->install();
		   }
	   }*/

	/*public function uninstall(): void {
		   if ($this->user->hasPermission('modify', 'extension/payment')) {
			   $this->load->model('extension/oc_payment_example/payment/credit_card');

			   $this->model_extension_oc_payment_example_payment_credit_card->uninstall();
		   }
	   }*/
}