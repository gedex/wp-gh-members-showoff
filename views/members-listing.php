<?php $org = $members['org_info']; // Shortcut ?>
<div class="gh-members-showoff-shortcode">
	<nav class="orgs-nav">
		<a class="org-nav-item selected" href="<?php echo esc_url( $org['html_url'] ); ?>">
			<span class="octicon octicon-organization"></span>
			<?php _e( 'Members', 'github-api' ); ?>
			<span class="count"><?php echo esc_html( $members['count'] ); ?></span>
		</a>
	</nav>

	<h1 class="org-title">
		<a class="org-link js-org-name" href="/orgs/woothemes" data-name="<?php echo esc_html( $org['login'] ); ?>">
			<img alt="<?php echo esc_attr( $org['name'] ); ?>" class="avatar" data-user="<?php echo esc_attr( $org['id'] ); ?>" height="30" itemprop="image" src="<?php echo esc_url( $org['avatar_url'] ); ?>" width="30">
			<?php echo esc_html( $org['name'] ); ?>
		</a>
	</h1>

	<div id="org-members">
		<ul class="member-listing">
			<?php foreach ( $members['data'] as $member ): ?>
			<li class="member-list-item" data-id="<?php echo esc_attr( $member['id'] ); ?>">
				<div class="member-info">
					<a class="member-link" href="<?php echo esc_url( $member['html_url'] ); ?>" itemprop="url">
						<img alt="<?php echo esc_attr( $member['login'] ); ?>" class="member-list-avatar" data-user="<?php echo esc_attr( $member['id'] ); ?>" height="48" src="<?php echo esc_url( $member['avatar_url'] ); ?>" width="48">
						<span class="member-username"><?php echo esc_html( $member['login'] ); ?></span>
						<?php
						/*
						<span class="member-fullname" itemprop="name"><?php echo esc_html( $member['name'] ); ?></span>
						*/
						?>
					</a>
				</div>
			</li><!-- /.member-item -->
			<?php endforeach; ?>
		</ul>
	</div><!-- /org-members -->

</div><!-- /gh-members-showoff-shortcode -->
