<?php
/**
 * Export Class
 *
 * This is the base class for all export methods. Each data export type (referrals, affiliates, visits) extend this class
 *
 * @package     Affiliate WP
 * @subpackage  Admin/Export
 * @copyright   Copyright (c) 2014, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Affiliate_WP_Export Class
 *
 * @since 1.0
 */
class Affiliate_WP_Referral_Export extends Affiliate_WP_Export {

	/**
	 * Our export type. Used for export-type specific filters/actions
	 * @var string
	 * @since 1.0
	 */
	public $export_type = 'referrals';

	/**
	 * Begin date
	 * @var string
	 * @since 1.0
	 */
	public $start_date;

	/**
	 * End date
	 * @var string
	 * @since 1.0
	 */
	public $end_date;


	/**
	 * Status
	 * @var string
	 * @since 1.0
	 */
	public $status;

	/**
	 * Set the CSV columns
	 *
	 * @access public
	 * @since 1.0
	 * @return array $cols All the columns
	 */
	public function csv_cols() {
		$cols = array(
			'email'    => __( 'Email', 'affiliate-wp' ),
			'amount'   => __( 'Amount', 'affiliate-wp' ),
			'currency' => __( 'Currency', 'affiliate-wp' ),
			'date'     => __( 'Date', 'affiliate-wp' )
		);
		return $cols;
	}

	/**
	 * Get the data being exported
	 *
	 * @access public
	 * @since 1.0
	 * @return array $data Data for Export
	 */
	public function get_data() {

		$args = array(

			'status' => ! empty( $this->status ) ? $this->status : '',
			'date'   => ! empty( $this->date )   ? $this->date   : '',

		);

		$data      = array();
		$referrals = affiliate_wp()->referrals->get_referrals( $args );

		if( $referrals ) {

			foreach( $referrals as $referral ) {

				$data[] = array(
					'email'    => affwp_get_affiliate_email( $referral->affiliate_id ),
					'amount'   => $referral->amount,
					'currency' => $referral->currency,
					'date'     => $referral->date,
				);

			}

		}

		$data = apply_filters( 'affwp_export_get_data', $data );
		$data = apply_filters( 'affwp_export_get_data_' . $this->export_type, $data );

		return $data;
	}

}