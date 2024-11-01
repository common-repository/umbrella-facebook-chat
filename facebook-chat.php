<?php
/*
Plugin Name: Facebook Chat for WordPress
Plugin URI: https://www.umbrellabranding.gr/facebook-chat-wordpress/
Description: This plugin allows your visitors chat from your website directly to your Facebook Page. It uses the new Facebook feature and its available on desktops and mobile devices.
Author: Umbrella Branding
Author URI: https://www.umbrellabranding.gr
Version: 1.0
License: GPLv2
*/

if ( ! defined( 'ABSPATH' ) )
	exit;

function facebook_chat_menu() {
	add_options_page('Facebook Chat for WordPress', 'Facebook Chat', 'administrator', 'facebook_chat-settings', 'facebook_chat_settings_page');
}
add_action('admin_menu', 'facebook_chat_menu');

function facebook_chat_settings_page() { ?>
<div class="wrap">
<h2>Facebook Chat for WordPress Settings</h2>
<p>This plugin allows your visitors chat from your website directly to your Facebook Page. It uses the new Facebook feature and its available on desktops and mobile devices.
</p>
<form method="post" action="options.php">
    <?php
		settings_fields( 'facebook_chat-settings' );
		do_settings_sections( 'facebook_chat-settings' );
	?>


    <table class="form-table">
		<tr valign="top">
			<th scope="row"><label for="facebook_chat_enable">Enable Facebook Chat</label></th>
			<td>
				<input type="checkbox" name="facebook_chat_enable" value="true" <?php echo ( get_option('facebook_chat_enable') == true ) ? ' checked="checked" />' : ' />'; ?>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="facebook_chat_pageID">Facebook Page ID</label></th>
			<td>
				<input type="text" size="20" name="facebook_chat_pageID" value="<?php echo esc_attr( get_option('facebook_chat_pageID') ); ?>" /><br /><small><a href="https://findmyfbid.com/" target="_blank">What is my Facebook Page ID?</a></small>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="facebook_chat_appID">Application ID</label></th>
			<td>
				<input type="text" size="20" name="facebook_chat_appID" value="<?php echo esc_attr( get_option('facebook_chat_appID') ); ?>" /><br /><small><a href="https://developers.facebook.com/" target="_blank">Create a new Facebook App</a></small>
			</td>
		</tr>		
		<tr valign="top">
			<th scope="row"><label for="facebook_chat_lang"><p>Current Language Code</label></th>
			<td> 
				<?php
					$filename = __DIR__.'/FacebookLocales.json';
					
					if (ini_get('allow_url_fopen')) {
						if(file_exists($filename)) {
							$langs      = file_get_contents($filename);
							$jsoncont   = json_decode($langs);
						?>
							<p>
								<label for="facebook_chat_lang"></label>
								<select name="facebook_chat_lang" id="facebook_chat_lang">
									<?php
									if (!empty($jsoncont)) {
										foreach ($jsoncont as $languages=>$short_name) { ?>
											<option <?php echo ( get_option('facebook_chat_lang') == $short_name ) ? ' selected="selected"' : ''; ?> value="<?php echo $short_name; ?>"><?php _e($languages); ?></option>
											<?php
										}
									}
									?>
								</select>
							</p>
						<?php
						}
					} else {
						?>
						<p>Your PHP configuration does not allow to read <a href="<?php echo plugin_dir_url(__FILE__).'FacebookLocales.json';?>" target="_blank">this</a> file.
							To unable language option, enable <a href="http://php.net/manual/en/filesystem.configuration.php#ini.allow-url-fopen" target="_blank"><b>allow_url_fopen</b></a> in your server configuration.
						</p>
						<?php
					}
				?>
			</td>
		</tr>	
    </table>
	
    <?php submit_button(); ?>
</form>
<p><a href="https://www.umbrellabranding.gr/" style="color:#000; text-decoration:none" target="_blank">Developed by <img src="<?php echo plugins_url( 'images/umbrellabranding.png', __FILE__ ) ?>" style="top: 8px; position: relative;"/> Umbrella Branding</a></p>
</div>
<?php }

function facebook_chat_settings() {
	register_setting( 'facebook_chat-settings', 'facebook_chat_enable' );
	register_setting( 'facebook_chat-settings', 'facebook_chat_pageID' );
	register_setting( 'facebook_chat-settings', 'facebook_chat_lang' );
	register_setting( 'facebook_chat-settings', 'facebook_chat_oldlang' );
	register_setting( 'facebook_chat-settings', 'facebook_chat_appID' );
}
add_action( 'admin_init', 'facebook_chat_settings' );

function facebook_chat_deactivation() {
    delete_option( 'facebook_chat_enable' );
    delete_option( 'facebook_chat_pageID' );
    delete_option( 'facebook_chat_lang' );
    delete_option( 'facebook_chat_oldlang' );
    delete_option( 'facebook_chat_appID' );
}
register_deactivation_hook( __FILE__, 'facebook_chat_deactivation' );

function facebook_chat() { ?>

<?php if (get_option('facebook_chat_enable') == true) { ?>
<div class="fb-customerchat" page_id="<?php echo esc_attr( get_option('facebook_chat_pageID') );?>"></div>


    <script>
 
      window.fbAsyncInit = function() {
        FB.init({
          appId            : '<?php echo esc_attr( get_option('facebook_chat_appID') );?>',
          autoLogAppEvents : true,
          xfbml            : true,
          version          : 'v2.11'
        });
      };
 
      (function(d, s, id){
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) {return;}
        js = d.createElement(s); js.id = id;
        js.src = "https://connect.facebook.net/<?php if (get_option('facebook_chat_lang')=='blank'){ echo "en_US"; }else{ echo esc_attr( get_option('facebook_chat_lang') ); } ?>/sdk.js";
        fjs.parentNode.insertBefore(js, fjs);
      }(document, 'script', 'facebook-jssdk'));
      
    </script>  
<?php } 
}
add_action( 'wp_footer', 'facebook_chat', 10 );