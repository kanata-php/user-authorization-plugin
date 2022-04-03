
<?php $this->layout('auth::emails/layouts/base') ?>

<p>Hello <?=$name?></p>
<p>Thankyou for registering with <?=config('app.app-name')?>.</p>
<p>To proceed please confirm your email clicking in the following link or copying and pasting its url:</p>
<p><a href="<?=$email_confirmation_url?>"><?=$email_confirmation_url?></a></p>
