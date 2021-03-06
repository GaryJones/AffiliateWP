<?php


function affwp_get_referral( $referral ) {

	if( is_object( $referral ) && isset( $referral->referral_id ) ) {
		$referral_id = $referral->referral_id;
	} elseif( is_numeric( $referral ) ) {
		$referral_id = absint( $referral );
	} else {
		return false;
	}

	return affiliate_wp()->referrals->get( $referral_id );
}

function affwp_get_referral_status( $referral ) {

	if( is_object( $referral ) && isset( $referral->referral_id ) ) {
		$referral_id = $referral->referral_id;
	} elseif( is_numeric( $referral ) ) {
		$referral_id = absint( $referral );
	} else {
		return false;
	}

	return affiliate_wp()->referrals->get_column( 'status', $referral_id );
}

function affwp_set_referral_status( $referral, $new_status = '' ) {

	if( is_object( $referral ) && isset( $referral->referral_id ) ) {
		$referral_id = $referral->referral_id;
	} elseif( is_numeric( $referral ) ) {
		$referral_id = absint( $referral );
		$referral    = affwp_get_referral( $referral_id );
	} else {
		return false;
	}

	$old_status = $referral->status;

	if( $old_status == $new_status ) {
		return false;
	}

	if( 'paid' == $new_status || ( 'unpaid' == $new_status && 'pending' == $old_status ) ) {

		affwp_increase_affiliate_earnings( $referral->affiliate_id, $referral->amount );
		affwp_increase_affiliate_referral_count( $referral->affiliate_id );
	
	} elseif( 'rejected' == $new_status || 'unpaid' == $new_status ) {

		affwp_decrease_affiliate_referral_count( $referral->affiliate_id );

	}

	if( affiliate_wp()->referrals->update( $referral_id, array( 'status' => $new_status ) ) ) {

		do_action( 'affwp_set_referral_status', $referral_id, $new_status, $old_status );
	
		return true;
	}

	return false;

}

function affwp_delete_referral( $referral ) {

	if( is_object( $referral ) && isset( $referral->referral_id ) ) {
		$referral_id = $referral->referral_id;
	} elseif( is_numeric( $referral ) ) {
		$referral_id = absint( $referral );
		$referral    = affwp_get_referral( $referral_id );
	} else {
		return false;
	}

	if( 'paid' == $referral->status ) {
		
		// This referral has already been paid, so decrease the affiliate's earnings
		affwp_decrease_affiliate_earnings( $referral->affiliate_id, $referral->amount );
		
		// Decrease the referral count
		affwp_decrease_affiliate_referral_count( $referral->affiliate_id );

	}

	if( affiliate_wp()->referrals->delete( $referral_id ) ) {

		do_action( 'affwp_delete_referral', $referral_id );

		return true;

	}

	return false;
}

function affwp_calc_referral_amount( $amount = '', $affiliate_id = 0, $reference = 0 ) {

	return round( $amount * affwp_get_affiliate_rate( $affiliate_id ), 2 );
}

function affwp_count_referrals( $affiliate_id = 0, $status = array(), $date = array() ) {

	$args = array(
		'affiliate_id' => $affiliate_id,
		'status' => $status
	);

	if( ! empty( $date ) ) {
		$args['date'] = $date;
	}

	return affiliate_wp()->referrals->count( $args );

}