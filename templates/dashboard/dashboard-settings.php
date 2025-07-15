<?php
/**
 * Account settings
 */
global $current_user, $workreap_settings;

$reference = !empty($_GET['ref']) ? esc_html($_GET['ref']) : '';
$mode = !empty($_GET['mode']) ? esc_html($_GET['mode']) : '';

// Allowed modes
$allowed_modes = array('profile','verification','billing','account','portfolios','update-portfolio','skills');

if (!in_array($mode, $allowed_modes)) {
	$reference = 'dashboard';
	$mode = 'profile';
}

if (!empty($reference) && $reference != 'dashboard') {
	$reference = 'dashboard';
	$mode = 'profile';
}

$user_identity = intval($current_user->ID);
$id = !empty($args['id']) ? intval($args['id']) : '';
$user_type = apply_filters('workreap_get_user_type', $user_identity);
$identity_verification = !empty($workreap_settings['identity_verification']) ? $workreap_settings['identity_verification'] : false;
?>
<div class="wr-settings-page-wrap">
	<div class="row">
		<div class="col-lg-4 col-xl-3">
			<aside>
				<div class="wr-asideholder">
					<div class="wr-asidebox wr-settingtabholder">
						<ul class="wr-settingtab">
							<li class="<?php echo esc_attr($mode === 'profile' ? 'active' : ''); ?>">
								<a href="<?php echo esc_url(Workreap_Profile_Menu::workreap_profile_menu_link('dashboard', $user_identity, true, 'profile')); ?>">
									<i class="wr-icon-user"></i><?php esc_html_e('Profile settings','workreap'); ?>
								</a>
							</li>
							
							<?php if ($user_type === 'freelancers') { ?>
								<li class="<?php echo esc_attr(in_array($mode, ['portfolios','update-portfolio']) ? 'active' : ''); ?>">
									<a href="<?php echo esc_url(Workreap_Profile_Menu::workreap_profile_menu_link('dashboard', $user_identity, true, 'portfolios')); ?>">
										<i class="wr-icon-edit"></i><?php esc_html_e('Manage Portfolio','workreap'); ?>
									</a>
								</li>

								<!-- ✅ New "Job Matching Preferences" menu item -->
								<li class="<?php echo esc_attr($mode === 'skills' ? 'active' : ''); ?>">
									<a href="<?php echo esc_url(Workreap_Profile_Menu::workreap_profile_menu_link('dashboard', $user_identity, true, 'skills')); ?>">
										<i class="wr-icon-briefcase"></i><?php esc_html_e('Job Matching Preferences','workreap'); ?>
									</a>
								</li>
							<?php } ?>

							<?php if (!empty($identity_verification)) { ?>
								<li class="<?php echo esc_attr($mode === 'verification' ? 'active' : ''); ?>">
									<a href="<?php echo esc_url(Workreap_Profile_Menu::workreap_profile_menu_link('dashboard', $user_identity, true, 'verification')); ?>">
										<i class="wr-icon-check-square"></i><?php esc_html_e('Identity verification','workreap'); ?>
									</a>
								</li>
							<?php } ?>

							<li class="<?php echo esc_attr($mode === 'billing' ? 'active' : ''); ?>">
								<a href="<?php echo esc_url(Workreap_Profile_Menu::workreap_profile_menu_link('dashboard', $user_identity, true, 'billing')); ?>">
									<i class="wr-icon-credit-card"></i><?php esc_html_e('Billing information','workreap'); ?>
								</a>
							</li>

							<li class="<?php echo esc_attr($mode === 'account' ? 'active' : ''); ?>">
								<a href="<?php echo esc_url(Workreap_Profile_Menu::workreap_profile_menu_link('dashboard', $user_identity, true, 'account')); ?>">
									<i class="wr-icon-settings"></i><?php esc_html_e('Account settings','workreap'); ?>
								</a>
							</li>
						</ul>
					</div>
				</div>
			</aside>
		</div>

		<div class="col-lg-8 col-xl-9">
			<?php
			if ($mode === 'billing') {
				workreap_get_template_part('dashboard/dashboard', 'billing-settings');
			} elseif ($mode === 'profile') {
				if ($user_type === 'freelancers') {
					workreap_get_template_part('dashboard/dashboard', 'profile-settings');
					workreap_get_template_part('dashboard/dashboard', 'education');
					workreap_get_template_part('dashboard/dashboard', 'experience');
				} else {
					workreap_get_template_part('dashboard/dashboard', 'employer-setting');
				}
			} elseif ($mode === 'account') {
				workreap_get_template_part('dashboard/dashboard', 'account-settings');
			} elseif ($mode === 'portfolios') {
				workreap_get_template_part('dashboard/dashboard', 'list-portfolio');
			} elseif ($mode === 'update-portfolio') {
				workreap_get_template_part('dashboard/dashboard', 'update-portfolio');
			} elseif ($mode === 'verification') {
				workreap_get_template_part('dashboard/dashboard', 'identity-verification');
			} elseif ($mode === 'skills') {
				workreap_get_template_part('dashboard/dashboard', 'update-skills');
			}
			?>
		</div>
	</div>
</div>
