/**
 * External dependencies
 */
import { connect } from 'react-redux';
import { compose } from 'redux';
import moment from 'moment';

/**
 * Internal dependencies
 */
import Template from './template';
import { withStore } from '@moderntribe/common/hoc';
import { selectors, actions } from '@moderntribe/tickets/data/blocks/ticket';
import {
	globals,
	moment as momentUtil,
	time as timeUtil,
} from '@moderntribe/common/utils';

const onFromDateChange = ( dispatch, ownProps ) => ( date, modifiers, dayPickerInput ) => {
	dispatch( actions.handleTicketStartDate( ownProps.blockId, date, dayPickerInput ) )
};

const onFromTimePickerChange = ( dispatch, ownProps ) => ( e ) => {
	dispatch( actions.setTicketTempStartTimeInput( ownProps.blockId, e.target.value ) );
};

const onFromTimePickerClick = ( dispatch, ownProps ) => ( value, onClose ) => {
	dispatch( actions.handleTicketStartTime( ownProps.blockId, value ) );
	onClose();
};

const onToDateChange = ( dispatch, ownProps ) => ( date, modifiers, dayPickerInput ) => {
	dispatch( actions.handleTicketEndDate( ownProps.blockId, date, dayPickerInput ) )
};

const onToTimePickerChange = ( dispatch, ownProps ) => ( e ) => {
	dispatch( actions.setTicketTempEndTimeInput( ownProps.blockId, e.target.value ) );
};

const onToTimePickerClick = ( dispatch, ownProps ) => ( value, onClose ) => {
	dispatch( actions.handleTicketEndTime( ownProps.blockId, value ) );
	onClose();
};

const onFromTimePickerBlur = ( state, dispatch, ownProps ) => ( e ) => {
	let startTimeMoment = momentUtil.toMoment( e.target.value, momentUtil.TIME_FORMAT, false );
	if ( ! startTimeMoment.isValid() ) {
		const startTimeInput = selectors.getTicketStartTimeInput( state, ownProps )
		startTimeMoment = momentUtil.toMoment( startTimeInput, momentUtil.TIME_FORMAT, false );
	}
	const seconds = momentUtil.totalSeconds( startTimeMoment );
	dispatch( actions.handleTicketStartTime( ownProps.blockId, seconds ) );
};

const onToTimePickerBlur = ( state, dispatch, ownProps ) => ( e ) => {
	let endTimeMoment = momentUtil.toMoment( e.target.value, momentUtil.TIME_FORMAT, false );
	if ( ! endTimeMoment.isValid() ) {
		const endTimeInput = selectors.getTicketEndTimeInput( state, ownProps )
		endTimeMoment = momentUtil.toMoment( endTimeInput, momentUtil.TIME_FORMAT, false );
	}
	const seconds = momentUtil.totalSeconds( endTimeMoment );
	dispatch( actions.handleTicketEndTime( ownProps.blockId, seconds ) );
};

const mapStateToProps = ( state, ownProps ) => {
	const datePickerFormat = globals.tecDateSettings().datepickerFormat
		? momentUtil.toFormat( globals.tecDateSettings().datepickerFormat )
		: 'LL';
	const isDisabled = selectors.isTicketDisabled( state, ownProps );

	return {
		fromDate: selectors.getTicketTempStartDateInput( state, ownProps ),
		fromDateDisabled: isDisabled,
		fromDateFormat: datePickerFormat,
		fromTime: selectors.getTicketTempStartTimeNoSeconds( state, ownProps ),
		fromTimeDisabled: isDisabled,
		isSameDay: momentUtil.isSameDay(
			selectors.getTicketTempStartDateMoment( state, ownProps ),
			selectors.getTicketTempEndDateMoment( state, ownProps ),
		),
		toDate: selectors.getTicketTempEndDateInput( state, ownProps ),
		toDateDisabled: isDisabled,
		toDateFormat: datePickerFormat,
		toTime: selectors.getTicketTempEndTimeNoSeconds( state, ownProps ),
		toTimeDisabled: isDisabled,
		state,
	};
};

const mapDispatchToProps = ( dispatch, ownProps ) => ( {
	onFromDateChange: onFromDateChange( dispatch, ownProps ),
	onFromTimePickerChange: onFromTimePickerChange( dispatch, ownProps ),
	onFromTimePickerClick: onFromTimePickerClick( dispatch, ownProps ),
	onToDateChange: onToDateChange( dispatch, ownProps ),
	onToTimePickerChange: onToTimePickerChange( dispatch, ownProps ),
	onToTimePickerClick: onToTimePickerClick( dispatch, ownProps ),
	dispatch,
} );

const mergeProps = ( stateProps, dispatchProps, ownProps ) => {
	const { state, ...restStateProps } = stateProps;
	const { dispatch, ...restDispatchProps } = dispatchProps;

	return {
		...ownProps,
		...restStateProps,
		...restDispatchProps,
		onFromTimePickerBlur: onFromTimePickerBlur( state, dispatch, ownProps ),
		onToTimePickerBlur: onToTimePickerBlur( state, dispatch, ownProps ),
	};
}

export default compose(
	withStore(),
	connect(
		mapStateToProps,
		mapDispatchToProps,
		mergeProps,
	),
)( Template );
