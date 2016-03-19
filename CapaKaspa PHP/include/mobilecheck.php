<?	
require_once('mobile/class.detect_device.php');
$actual_device = new detect_device();
if (isset($_GET[ 'prevent_redirection' ])) {
	$actual_device->prevent_redirection();
}
$actual_device->mobileredirect = "http://jouerauxechecs.capakaspa.info/mobile";
$actual_device->desktopredirect = false;
$actual_device->redirect();
?>