<?php

class Affiliate_WP_Shopp extends Affiliate_WP_Base {
	
	public function init() {
		add_action( 'shopp_invoiced_order_event', array( $this, 'add_pending_referral' ), 10 );
		add_action( 'shopp_captured_order_event', array( $this, 'mark_referral_complete' ), 10 );
		add_action( 'shopp_refunded_order_event', array( $this, 'revoke_referral_on_refund' ), 10 );
		add_action( 'shopp_voided_order_event', array( $this, 'revoke_referral_on_refund' ), 10 );
		add_action( 'shopp_delete_purchase', array( $this, 'revoke_referral_on_delete' ), 10 );
	}

	public function add_pending_referral( $order_id = 0 ) {


		if( $this->was_referred() ) {

			$this->insert_pending_referral( $amount, $order_id );
		}

	}

	public function mark_referral_complete( $order_id = 0 ) {

		if( $order->is_transaction_completed() ) {

			$this->complete_referral( $order_id );

		}

		// TODO add order note about referral

	}

	public function revoke_referral_on_refund( $payment_id = 0, $new_status, $old_status ) {
	
		if( 'publish' != $old_status && 'revoked' != $old_status ) {
			return;
		}

		if( 'refunded' != $new_status ) {
			return;
		}

		if( ! affiliate_wp()->settings->get( 'revoke_on_refund' ) ) {
			return;
		}

		$this->reject_referral( $payment_id );

	}

	public function revoke_referral_on_delete( $payment_id = 0 ) {

		if( ! affiliate_wp()->settings->get( 'revoke_on_refund' ) ) {
			return;
		}

		$this->reject_referral( $payment_id );

	}
	
}
new Affiliate_WP_Shopp;