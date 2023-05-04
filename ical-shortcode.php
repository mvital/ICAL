/* add to functions.php or to plugin */

function generate_ical_file($start_date, $end_date, $start_time, $end_time, $location, $description) {
// Remove <p> tags from the description field
$description = strip_tags($description);
// Convert Unix timestamp to DateTime object
$start_datetime = DateTime::createFromFormat('U', $start_date);
$end_datetime = DateTime::createFromFormat('U', $end_date);
// Set the start and end times using setTime method
$start_time_parts = explode(':', $start_time);
$start_datetime->setTime($start_time_parts[0], $start_time_parts[1]);
$end_time_parts = explode(':', $end_time);
$end_datetime->setTime($end_time_parts[0], $end_time_parts[1]);
// Subtract two hours from the start and end times
$start_datetime->sub(new DateInterval('PT2H'));
$end_datetime->sub(new DateInterval('PT2H'));
$ical = "BEGIN:VCALENDAR\r\n";
$ical .= "VERSION:2.0\r\n";
$ical .= "BEGIN:VEVENT\r\n";
$ical .= "DTSTART:" . $start_datetime->format('Ymd\THis\Z') . "\r\n";
$ical .= "DTEND:" . $end_datetime->format('Ymd\THis\Z') . "\r\n";
$ical .= "SUMMARY:" . get_the_title() . "\r\n";
$ical .= "DESCRIPTION:" . $description . "\r\n";
$ical .= "LOCATION:" . $location . "\r\n";
$ical .= "END:VEVENT\r\n";
$ical .= "END:VCALENDAR\r\n";
return $ical;
}
function generate_ical_shortcode() {
global $post;
$start_date = get_post_meta($post->ID, 't_start_date_neu', true);
$end_date = get_post_meta($post->ID, 't_end_date_neu', true);
$start_time = get_post_meta($post->ID, 't_start_time_neu', true);
$end_time = get_post_meta($post->ID, 't_end_time_neu', true);
$location = get_post_meta($post->ID, 't_location', true);
$description = get_post_meta($post->ID, 't_beschreibung', true);
$ical = generate_ical_file($start_date, $end_date, $start_time, $end_time, $location, $description);
$filename = 'termin.ics';
$filepath = plugin_dir_path(__FILE__) . $filename;
if (file_put_contents($filepath, $ical)) {
$download_url = plugins_url($filename, __FILE__);
$button_html = '<a href="' . $download_url . '" class="icalbutton"><i class="fas fa-calendar-check" style="margin-right: 10px;"></i>Zum Kalender hinzufügen</a>';
return $button_html;
}
else {
return 'Error generating iCal file.';
}
}
add_shortcode('generate_ical', 'generate_ical_shortcode');
