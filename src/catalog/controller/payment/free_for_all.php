<?php
namespace Opencart\Catalog\Controller\Extension\FreeForAll\Payment;

class FreeForAll extends \Opencart\System\Engine\Controller
{

	/**
	 * @return string
	 */
	public function index(): string
	{
		$this->load->language('extension/free_for_all/payment/free_for_all');

		$data['language'] = $this->config->get('config_language');

		return $this->load->view('extension/free_for_all/payment/free_for_all', $data);
	}

	/**
	 * @return void
	 */
	public function confirm(): void
	{
		$this->load->language('extension/free_for_all/payment/free_for_all');

		$json = [];

		if (!isset($this->session->data['order_id'])) {
			$json['error'] = $this->language->get('error_order');
		}

		if (!isset($this->session->data['payment_method']) || $this->session->data['payment_method']['code'] != 'free_for_all.free_for_all') {
			$json['error'] = $this->language->get('error_payment_method');
		}

		if (!$json) {
			$this->load->model('checkout/order');

			$this->model_checkout_order->addHistory($this->session->data['order_id'], $this->config->get('payment_free_for_all_order_status_id'));

			$json['redirect'] = $this->url->link('checkout/success', 'language=' . $this->config->get('config_language'), true);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));

		$this->output_information();

		//$this->send_message();
		$this->send_http_get();
	}

	/**
	 * Output information about the customer and the order.
	 */
	private function output_information(): void
	{
		$bestellung = $this->session->data['order_id'];
		$this->log->write('Bestellung Nr. ' . $bestellung);
		$kunde = $this->session->data['customer_id'];
		$this->log->write('Kunde Nr. ' . $kunde);
		foreach ($this->cart->getProducts() as $product) {
			$produkt = $product['product_id'];
			$produktname = $product['name'] . ' ' . $product['model'];
			$this->log->write('Produkt Nr. ' . $produkt . ' (' . $produktname . ')');
			if (isset($product['option'])) {
				foreach ($product['option'] as $o) {
					$option_wert = $o['value'];
					$this->log->write('Option: ' . $option_wert);
				}
				//$optionen = json_encode($product['option']);
				//$this->log->write('Optionen: ' . $optionen);
			}
		}
		//$this->log->write('Inhalt des Warenkorbs: ' . json_encode($this->cart->getProducts()));
	}

	private function send_http_get(): void {
		$ch = curl_init();
		$url = 'http://192.168.10.52:4444';
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		$result = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
	}

	/**
	 * Send information to MES system.
	 */
	private function send_message(): void
	{
		// get the port for the WWW service
		$service_port = 4444;

		// get the IP address for the target host
		$address = '192.168.10.52'; // gethostbyname('www.example.com');

		// create a TCP/IP socket
		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		if ($socket === false) {
			$this->log->write("socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n");
		} else {
			$this->log->write("OK.\n");
		}

		$this->log->write("Attempting to connect to '$address' on port '$service_port'...");
		$result = socket_connect($socket, $address, $service_port);
		if ($result === false) {
			$this->log->write("socket_connect() failed.\nReason: ($result) " . socket_strerror(socket_last_error($socket)) . "\n");
		} else {
			$this->log->write("OK.\n");
		}

		// collect all information to be sent to MES system
		$bestellung = $this->session->data['order_id'];
		$kunde = $this->session->data['customer_id'];
		$produkte = '';
		foreach ($this->cart->getProducts() as $produkt) {
			$produkte .= $produkt['product_id'];
		}
		$message = 'Data: ' . $bestellung . ',' . $kunde . ',' . $produkte . "\r\n";

		// write information to socket
		$this->log->write("Sending data...");
		socket_write($socket, $message, strlen($message));
		$this->log->write("OK.\n");

		//$this->log->write("Reading response:\n\n");
		//$out = '';
		//while ($out = socket_read($socket, 2048)) {
		//	$this->log->write($out);
		//}

		$this->log->write("Closing socket...");
		socket_close($socket);
		$this->log->write("OK.\n\n");
	}
}
