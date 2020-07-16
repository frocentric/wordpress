<?php


class Tribe__Events__Pro__Recurrence__Strings {

	/**
	 * Build possible strings for recurrence
	 *
	 * @return array
	 */
	public static function recurrence_strings() {
		$strings = array(
			'same-time-text-same-all-day'              => __( 'All day', 'tribe-events-calendar-pro' ),
			'same-time-text-same'                      => __( '%1$s', 'tribe-events-calendar-pro' ),
			'same-day-month-1'                         => __( 'the 1st day of the month', 'tribe-events-calendar-pro' ),
			'same-day-month-2'                         => __( 'the 2nd day of the month', 'tribe-events-calendar-pro' ),
			'same-day-month-3'                         => __( 'the 3rd day of the month', 'tribe-events-calendar-pro' ),
			'same-day-month-4'                         => __( 'the 4th day of the month', 'tribe-events-calendar-pro' ),
			'same-day-month-5'                         => __( 'the 5th day of the month', 'tribe-events-calendar-pro' ),
			'same-day-month-6'                         => __( 'the 6th day of the month', 'tribe-events-calendar-pro' ),
			'same-day-month-7'                         => __( 'the 7th day of the month', 'tribe-events-calendar-pro' ),
			'same-day-month-8'                         => __( 'the 8th day of the month', 'tribe-events-calendar-pro' ),
			'same-day-month-9'                         => __( 'the 9th day of the month', 'tribe-events-calendar-pro' ),
			'same-day-month-10'                        => __( 'the 10th day of the month', 'tribe-events-calendar-pro' ),
			'same-day-month-11'                        => __( 'the 11th day of the month', 'tribe-events-calendar-pro' ),
			'same-day-month-12'                        => __( 'the 12th day of the month', 'tribe-events-calendar-pro' ),
			'same-day-month-13'                        => __( 'the 13th day of the month', 'tribe-events-calendar-pro' ),
			'same-day-month-14'                        => __( 'the 14th day of the month', 'tribe-events-calendar-pro' ),
			'same-day-month-15'                        => __( 'the 15th day of the month', 'tribe-events-calendar-pro' ),
			'same-day-month-16'                        => __( 'the 16th day of the month', 'tribe-events-calendar-pro' ),
			'same-day-month-17'                        => __( 'the 17th day of the month', 'tribe-events-calendar-pro' ),
			'same-day-month-18'                        => __( 'the 18th day of the month', 'tribe-events-calendar-pro' ),
			'same-day-month-19'                        => __( 'the 19th day of the month', 'tribe-events-calendar-pro' ),
			'same-day-month-20'                        => __( 'the 20th day of the month', 'tribe-events-calendar-pro' ),
			'same-day-month-21'                        => __( 'the 21st day of the month', 'tribe-events-calendar-pro' ),
			'same-day-month-22'                        => __( 'the 22nd day of the month', 'tribe-events-calendar-pro' ),
			'same-day-month-23'                        => __( 'the 23rd day of the month', 'tribe-events-calendar-pro' ),
			'same-day-month-24'                        => __( 'the 24th day of the month', 'tribe-events-calendar-pro' ),
			'same-day-month-25'                        => __( 'the 25th day of the month', 'tribe-events-calendar-pro' ),
			'same-day-month-26'                        => __( 'the 26th day of the month', 'tribe-events-calendar-pro' ),
			'same-day-month-27'                        => __( 'the 27th day of the month', 'tribe-events-calendar-pro' ),
			'same-day-month-28'                        => __( 'the 28th day of the month', 'tribe-events-calendar-pro' ),
			'same-day-month-29'                        => __( 'the 29th day of the month', 'tribe-events-calendar-pro' ),
			'same-day-month-30'                        => __( 'the 30th day of the month', 'tribe-events-calendar-pro' ),
			'same-day-month-31'                        => __( 'the 31st day of the month', 'tribe-events-calendar-pro' ),

			// a single date
			'date-on'                                  => __( 'One event on [single_date] at [start_time]', 'tribe-events-calendar-pro' ),
			'date-allday-on'                           => __( 'One all day event on [single_date]', 'tribe-events-calendar-pro' ),
			'date-allday-on-at'                        => __( 'One all day event on [single_date] at [start_time]', 'tribe-events-calendar-pro' ),
			'date-multi-on'                            => __( 'One multi-day event starting on [single_date]', 'tribe-events-calendar-pro' ),
			'date-multi-on-at'                         => __( 'One multi-day event starting on [single_date] at [start_time]', 'tribe-events-calendar-pro' ),

			// daily, ending on a specific date
			'daily-on'                                 => __( 'An event every [interval] day(s) that begins at [start_time], repeating until [series_end_date]', 'tribe-events-calendar-pro' ),
			'daily-allday-on'                          => __( 'An all day event every [interval] day(s), repeating until [series_end_date]', 'tribe-events-calendar-pro' ),
			'daily-allday-on-at'                       => __( 'An all day event every [interval] day(s) at [start_time], repeating until [series_end_date]', 'tribe-events-calendar-pro' ),
			'daily-multi-on'                           => __( 'A multi-day event every [interval] day(s), repeating until [series_end_date]', 'tribe-events-calendar-pro' ),
			'daily-multi-on-at'                        => __( 'A multi-day event every [interval] day(s) at [start_time], repeating until [series_end_date]', 'tribe-events-calendar-pro' ),

			// daily, after a specific number of events
			'daily-after'                              => __( 'An event every [interval] day(s) that begins at [start_time], happening [count] times', 'tribe-events-calendar-pro' ),
			'daily-allday-after'                       => __( 'An all day event every [interval] day(s), happening [count] times', 'tribe-events-calendar-pro' ),
			'daily-allday-after-at'                    => __( 'An all day event every [interval] day(s) at [start_time], happening [count] times', 'tribe-events-calendar-pro' ),
			'daily-multi-after'                        => __( 'A multi-day event every [interval] day(s), happening [count] times', 'tribe-events-calendar-pro' ),
			'daily-multi-after-at'                     => __( 'A multi-day event every [interval] day(s) at [start_time], happening [count] times', 'tribe-events-calendar-pro' ),

			// daily, never ending
			'daily-never'                              => __( 'An event every [interval] day(s) that begins at [start_time], repeating indefinitely', 'tribe-events-calendar-pro' ),
			'daily-allday-never'                       => __( 'An all day event every [interval] day(s), repeating indefinitely', 'tribe-events-calendar-pro' ),
			'daily-allday-never-at'                    => __( 'An all day event every [interval] day(s) at [start_time], repeating indefinitely', 'tribe-events-calendar-pro' ),
			'daily-multi-never'                        => __( 'A multi-day event every [interval] day(s), repeating indefinitely', 'tribe-events-calendar-pro' ),
			'daily-multi-never-at'                     => __( 'A multi-day event every [interval] day(s) at [start_time], repeating indefinitely', 'tribe-events-calendar-pro' ),

			// weekly, ending on a specific date
			'weekly-on'                                => __( 'An event every [interval] week(s) that begins at [start_time] on [days_of_week], repeating until [series_end_date]', 'tribe-events-calendar-pro' ),
			'weekly-allday-on'                         => __( 'An all day event every [interval] week(s) on [days_of_week], repeating until [series_end_date]', 'tribe-events-calendar-pro' ),
			'weekly-allday-on-at'                      => __( 'An all day event every [interval] week(s) on [days_of_week] at [start_time], repeating until [series_end_date]', 'tribe-events-calendar-pro' ),
			'weekly-multi-on'                          => __( 'A multi-day event every [interval] week(s) starting on [days_of_week], repeating until [series_end_date]', 'tribe-events-calendar-pro' ),
			'weekly-multi-on-at'                       => __( 'A multi-day event every [interval] week(s) starting on [days_of_week] at [start_time], repeating until [series_end_date]', 'tribe-events-calendar-pro' ),

			// weekly, after a specific number of events
			'weekly-after'                             => __( 'An event every [interval] week(s) that begins at [start_time] on [days_of_week], happening [count] times', 'tribe-events-calendar-pro' ),
			'weekly-allday-after'                      => __( 'An all day event every [interval] week(s) on [days_of_week], happening [count] times', 'tribe-events-calendar-pro' ),
			'weekly-allday-after-at'                   => __( 'An all day event every [interval] week(s) on [days_of_week] at [start_time], happening [count] times', 'tribe-events-calendar-pro' ),
			'weekly-multi-after'                       => __( 'A multi-day event every [interval] week(s) starting on [days_of_week], happening [count] times', 'tribe-events-calendar-pro' ),
			'weekly-multi-after-at'                    => __( 'A multi-day event every [interval] week(s) starting on [days_of_week] at [start_time], happening [count] times', 'tribe-events-calendar-pro' ),

			// weekly, never ending
			'weekly-never'                             => __( 'An event every [interval] week(s) that begins at [start_time] on [days_of_week], repeating indefinitely', 'tribe-events-calendar-pro' ),
			'weekly-allday-never'                      => __( 'An all day event every [interval] week(s) on [days_of_week], repeating indefinitely', 'tribe-events-calendar-pro' ),
			'weekly-allday-never-at'                   => __( 'An all day event every [interval] week(s) on [days_of_week] at [start_time], repeating indefinitely', 'tribe-events-calendar-pro' ),
			'weekly-multi-never'                       => __( 'A multi-day event every [interval] week(s) starting on [days_of_week], repeating indefinitely', 'tribe-events-calendar-pro' ),
			'weekly-multi-never-at'                    => __( 'A multi-day event every [interval] week(s) starting on [days_of_week] at [start_time], repeating indefinitely', 'tribe-events-calendar-pro' ),

			// monthly, with a relative day, ending on a specific date
			'monthly-on'                               => __( 'An event every [interval] month(s) that begins at [start_time] on [month_day_description], repeating until [series_end_date]', 'tribe-events-calendar-pro' ),
			'monthly-allday-on'                        => __( 'An all day event every [interval] month(s) on [month_day_description], repeating until [series_end_date]', 'tribe-events-calendar-pro' ),
			'monthly-allday-on-at'                     => __( 'An all day event every [interval] month(s) on [month_day_description] at [start_time], repeating until [series_end_date]', 'tribe-events-calendar-pro' ),
			'monthly-multi-on'                         => __( 'A multi-day event every [interval] month(s) starting on [month_day_description], repeating until [series_end_date]', 'tribe-events-calendar-pro' ),
			'monthly-multi-on-at'                      => __( 'A multi-day event every [interval] month(s) starting on [month_day_description] at [start_time], repeating until [series_end_date]', 'tribe-events-calendar-pro' ),

			// monthly, with a numeric day, ending on a specific date
			'monthly-numeric-on'                       => __( 'An event every [interval] month(s) that begins at [start_time] on day [month_number] of the month, repeating until [series_end_date]', 'tribe-events-calendar-pro' ),
			'monthly-allday-numeric-on'                => __( 'An all day event every [interval] month(s) on day [month_number] of the month, repeating until [series_end_date]', 'tribe-events-calendar-pro' ),
			'monthly-allday-numeric-on-at'             => __( 'An all day event every [interval] month(s) on day [month_number] of the month at [start_time], repeating until [series_end_date]', 'tribe-events-calendar-pro' ),
			'monthly-multi-numeric-on'                 => __( 'A multi-day event every [interval] month(s) starting on day [month_number] of the month, repeating until [series_end_date]', 'tribe-events-calendar-pro' ),
			'monthly-multi-numeric-on-at'              => __( 'A multi-day event every [interval] month(s) starting on day [month_number] of the month at [start_time], repeating until [series_end_date]', 'tribe-events-calendar-pro' ),

			// monthly, with a relative day, after a specific number of events
			'monthly-after'                            => __( 'An event every [interval] month(s) that begins at [start_time] on [month_day_description], happening [count] times', 'tribe-events-calendar-pro' ),
			'monthly-allday-after'                     => __( 'An all day event every [interval] month(s) on [month_day_description], happening [count] times', 'tribe-events-calendar-pro' ),
			'monthly-allday-after-at'                  => __( 'An all day event every [interval] month(s) on [month_day_description] at [start_time], happening [count] times', 'tribe-events-calendar-pro' ),
			'monthly-multi-after'                      => __( 'A multi-day event every [interval] month(s) starting on [month_day_description], happening [count] times', 'tribe-events-calendar-pro' ),
			'monthly-multi-after-at'                   => __( 'A multi-day event every [interval] month(s) starting on [month_day_description] at [start_time], happening [count] times', 'tribe-events-calendar-pro' ),

			// monthly, with a numeric day, after a specific number of events
			'monthly-numeric-after'                    => __( 'An event every [interval] month(s) that begins at [start_time] on day [month_number] of the month, happening [count] times', 'tribe-events-calendar-pro' ),
			'monthly-allday-numeric-after'             => __( 'An all day event every [interval] month(s) on day [month_number] of the month, happening [count] times', 'tribe-events-calendar-pro' ),
			'monthly-allday-numeric-after-at'          => __( 'An all day event every [interval] month(s) on day [month_number] of the month at [start_time], happening [count] times', 'tribe-events-calendar-pro' ),
			'monthly-multi-numeric-after'              => __( 'A multi-day event every [interval] month(s) starting on day [month_number] of the month, happening [count] times', 'tribe-events-calendar-pro' ),
			'monthly-multi-numeric-after-at'           => __( 'A multi-day event every [interval] month(s) starting on day [month_number] of the month at [start_time], happening [count] times', 'tribe-events-calendar-pro' ),

			// monthly, with a relative day, never ending
			'monthly-never'                            => __( 'An event every [interval] month(s) that begins at [start_time] on [month_day_description], repeating indefinitely', 'tribe-events-calendar-pro' ),
			'monthly-allday-never'                     => __( 'An all day event every [interval] month(s) on [month_day_description], repeating indefinitely', 'tribe-events-calendar-pro' ),
			'monthly-allday-never-at'                  => __( 'An all day event every [interval] month(s) on [month_day_description] at [start_time], repeating indefinitely', 'tribe-events-calendar-pro' ),
			'monthly-multi-never'                      => __( 'A multi-day event every [interval] month(s) starting on [month_day_description], repeating indefinitely', 'tribe-events-calendar-pro' ),
			'monthly-multi-never-at'                   => __( 'A multi-day event every [interval] month(s) starting on [month_day_description] at [start_time], repeating indefinitely', 'tribe-events-calendar-pro' ),

			// monthly, with a numeric day, never ending
			'monthly-numeric-never'                    => __( 'An event every [interval] month(s) that begins at [start_time] on day [month_number] of the month, repeating indefinitely', 'tribe-events-calendar-pro' ),
			'monthly-allday-numeric-never'             => __( 'An all day event every [interval] month(s) on day [month_number] of the month, repeating indefinitely', 'tribe-events-calendar-pro' ),
			'monthly-allday-numeric-never-at'          => __( 'An all day event every [interval] month(s) on day [month_number] of the month at [start_time], repeating indefinitely', 'tribe-events-calendar-pro' ),
			'monthly-multi-numeric-never'              => __( 'A multi-day event every [interval] month(s) starting on day [month_number] of the month, repeating indefinitely', 'tribe-events-calendar-pro' ),
			'monthly-multi-numeric-never-at'           => __( 'A multi-day event every [interval] month(s) starting on day [month_number] of the month at [start_time], repeating indefinitely', 'tribe-events-calendar-pro' ),

			// yearly, with a relative day, ending on a specific date
			'yearly-on'                                => __( 'An event every [interval] year(s) that begins at [start_time] on [month_day_description] of [month_names], repeating until [series_end_date]', 'tribe-events-calendar-pro' ),
			'yearly-allday-on-at'                      => __( 'An all day event every [interval] year(s) on [month_day_description] of [month_names], repeating until [series_end_date]', 'tribe-events-calendar-pro' ),
			'yearly-allday-on'                         => __( 'An all day event every [interval] year(s) on [month_day_description] of [month_names] at [start_time], repeating until [series_end_date]', 'tribe-events-calendar-pro' ),
			'yearly-multi-on'                          => __( 'A multi-day event every [interval] year(s) starting on [month_day_description] of [month_names], repeating until [series_end_date]', 'tribe-events-calendar-pro' ),
			'yearly-multi-on-at'                       => __( 'A multi-day event every [interval] year(s) starting on [month_day_description] of [month_names] at [start_time], repeating until [series_end_date]', 'tribe-events-calendar-pro' ),

			// yearly, with a numeric day, ending on a specific date
			'yearly-numeric-on'                        => __( 'An event every [interval] year(s) that begins at [start_time] on day [month_number] of [month_names], repeating until [series_end_date]', 'tribe-events-calendar-pro' ),
			'yearly-allday-numeric-on'                 => __( 'An all day event every [interval] year(s) on day [month_number] of [month_names], repeating until [series_end_date]', 'tribe-events-calendar-pro' ),
			'yearly-allday-numeric-on-at'              => __( 'An all day event every [interval] year(s) on day [month_number] of [month_names] at [start_time], repeating until [series_end_date]', 'tribe-events-calendar-pro' ),
			'yearly-multi-numeric-on'                  => __( 'A multi-day event every [interval] year(s) starting on day [month_number] of [month_names], repeating until [series_end_date]', 'tribe-events-calendar-pro' ),
			'yearly-multi-numeric-on-at'               => __( 'A multi-day event every [interval] year(s) starting on day [month_number] of [month_names] at [start_time], repeating until [series_end_date]', 'tribe-events-calendar-pro' ),

			// yearly, with a relative day, after a specific number of events
			'yearly-after'                             => __( 'An event every [interval] year(s) that begins at [start_time] on [month_day_description] of [month_names], happening [count] times', 'tribe-events-calendar-pro' ),
			'yearly-allday-after'                      => __( 'An all day event every [interval] year(s) on [month_day_description] of [month_names], happening [count] times', 'tribe-events-calendar-pro' ),
			'yearly-allday-after-at'                   => __( 'An all day event every [interval] year(s) on [month_day_description] of [month_names] at [start_time], happening [count] times', 'tribe-events-calendar-pro' ),
			'yearly-multi-after'                       => __( 'A multi-day event every [interval] year(s) starting on [month_day_description] of [month_names], happening [count] times', 'tribe-events-calendar-pro' ),
			'yearly-multi-after-at'                    => __( 'A multi-day event every [interval] year(s) starting on [month_day_description] of [month_names] at [start_time], happening [count] times', 'tribe-events-calendar-pro' ),

			// yearly, with a numeric day, after a specific number of events
			'yearly-numeric-after'                     => __( 'An event every [interval] year(s) that begins at [start_time] on day [month_number] of [month_names], happening [count] times', 'tribe-events-calendar-pro' ),
			'yearly-allday-numeric-after'              => __( 'An all day event every [interval] year(s) on day [month_number] of [month_names], happening [count] times', 'tribe-events-calendar-pro' ),
			'yearly-allday-numeric-after-at'           => __( 'An all day event every [interval] year(s) on day [month_number] of [month_names] at [start_time], happening [count] times', 'tribe-events-calendar-pro' ),
			'yearly-multi-numeric-after'               => __( 'A multi-day event every [interval] year(s) starting on day [month_number] of [month_names], happening [count] times', 'tribe-events-calendar-pro' ),
			'yearly-multi-numeric-after-at'            => __( 'A multi-day event every [interval] year(s) starting on day [month_number] of [month_names] at [start_time], happening [count] times', 'tribe-events-calendar-pro' ),

			// yearly, with a relative day, never ending
			'yearly-never'                             => __( 'An event every [interval] year(s) that begins at [start_time] on [month_day_description] of [month_names], repeating indefinitely', 'tribe-events-calendar-pro' ),
			'yearly-allday-never'                      => __( 'An all day event every [interval] year(s) on [month_day_description] of [month_names], repeating indefinitely', 'tribe-events-calendar-pro' ),
			'yearly-allday-never-at'                   => __( 'An all day event every [interval] year(s) on [month_day_description] of [month_names] at [start_time], repeating indefinitely', 'tribe-events-calendar-pro' ),
			'yearly-multi-never'                       => __( 'A multi-day event every [interval] year(s) starting on [month_day_description] of [month_names], repeating indefinitely', 'tribe-events-calendar-pro' ),
			'yearly-multi-never-at'                    => __( 'A multi-day event every [interval] year(s) starting on [month_day_description] of [month_names] at [start_time], repeating indefinitely', 'tribe-events-calendar-pro' ),

			// yearly, with a numeric day, never ending
			'yearly-numeric-never'                     => __( 'An event every [interval] year(s) that begins at [start_time] on day [month_number] of [month_names], repeating indefinitely', 'tribe-events-calendar-pro' ),
			'yearly-allday-numeric-never'              => __( 'An all day event every [interval] year(s) on day [month_number] of [month_names], repeating indefinitely', 'tribe-events-calendar-pro' ),
			'yearly-allday-numeric-never-at'           => __( 'An all day event every [interval] year(s) on day [month_number] of [month_names] at [start_time], repeating indefinitely', 'tribe-events-calendar-pro' ),
			'yearly-multi-numeric-never'               => __( 'A multi-day event every [interval] year(s) starting on day [month_number] of [month_names], repeating indefinitely', 'tribe-events-calendar-pro' ),
			'yearly-multi-numeric-never-at'            => __( 'A multi-day event every [interval] year(s) starting on day [month_number] of [month_names] at [start_time], repeating indefinitely', 'tribe-events-calendar-pro' ),

			// confirmation dialog
			'delete-confirm'                           => __( 'Delete', 'tribe-events-calendar-pro' ),
			'delete-cancel'                            => __( 'Cancel', 'tribe-events-calendar-pro' ),
		);

		return $strings;
	}
}
