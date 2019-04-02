<?php
if(isset($row))
{
	$input_password_value = $this->input->get_value($row,$param.name);
}
?>
{if $param.update_check_original=='1' && isset($input_password_value) && $input_password_value}
<div class="input-password-group">
	<h6>原密码验证</h6>
	<input type="password" class="form-control" name="old_{$param.name}" placeholder="请输入原有密码" />
</div>
<div class="input-password-group">
	<h6>输入新密码</h6>
{/if}
	{if isset($input_password_value) && $input_password_value}
		<?php
		$input_password_original_session_key = $param.name . time() . rand(100,999);
		$_SESSION[$input_password_original_session_key] = $input_password_value;
		?>
		<input type="hidden" name="original_key_{$param.name}" value="{$input_password_original_session_key}" />
	{/if}
	<p>
		<input type="password" class="form-control" name="{$param.name}" placeholder="请输入新密码，留空表示不修改密码" />
	<p>
	{if $param.show_repeat==1}
	<p>
		<input type="password" class="form-control" name="repeat_{$param.name}" placeholder="请重复输入一遍" />
	</p>
	{/if}
{if $param.update_check_original=='1' && isset($input_password_value) && $input_password_value}
</div>
{/if}