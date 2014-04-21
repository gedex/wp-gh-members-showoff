<div class="gh-members-showoff">
	<h3 class="org-module-title org-members-title">
		<a class="org-module-link" href="<?php echo esc_url( sprintf( "https://github.com/orgs/%s/members", $instance['org'] ) ); ?>">
			<span class="org-stats"><?php echo $members['count']; ?>
				<span class="octicon octicon-chevron-right"></span>
			</span>
			<?php if ( ! empty( $instance['text_link'] ) ): ?>
				<?php echo $instance['text_link']; ?>
			<?php else: ?>
				<?php _e( 'Members', 'github-api' ); ?>
			<?php endif; ?>
		</a>
	</h3>
	<div class="member-avatar-group">
		<?php foreach ( $members['data'] as $member ): ?>
			<a href="<?php echo esc_url( $member['html_url'] ); ?>" class="member-avatar tooltipped tooltipped-s" aria-label="<?php echo esc_attr( $member['login'] ); ?>">
				<img alt="Akeda Bagus" class="member-avatar-img js-avatar" data-user="<?php echo esc_attr( $member['id'] ); ?>" height="48" src="<?php echo esc_url( $member['avatar_url'] ); ?>" width="48">
			</a>
		<?php endforeach; ?>
	</div>
</div>
