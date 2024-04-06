<?php
namespace Opencart\Catalog\Model\Extension\FreeForAll\Payment;

/**
 * Class FreeForAll
 *
 * @package
 */
class FreeForAll extends \Opencart\System\Engine\Model
{
	/**
	 * @param array $address
	 *
	 * @return array
	 */
	public function getMethods(array $address = []): array
	{
		$this->load->language('extension/free_for_all/payment/free_for_all');

		$option_data['free_for_all'] = [
			'code' => 'free_for_all.free_for_all',
			'name' => $this->language->get('heading_title')
		];

		$method_data = [
			'code' => 'free_for_all',
			'name' => $this->language->get('heading_title'),
			'option' => $option_data,
			'sort_order' => $this->config->get('payment_free_for_all_sort_order')
		];

		return $method_data;
	}
}
