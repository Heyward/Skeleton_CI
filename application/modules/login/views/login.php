    <!-- BEGIN CONTENT-->
        <div class="row">
            <br />
            <br />
            <div class="large-6 columns large-centered" style="background: #fff">
                
                <div class="row">
                    <div class="large-12 columns">
                        <h4>Login</h4>
                    </div>
                </div>

                <div class="row">
                    <div class="large-12 columns">
                        <div class="row">
                            <div class="large-12 columns" id="form-login">
                                <?php echo form_open('login/auth', 'id="login"');?>
                                    <div class="row-fluid">
                                        <span class="span3">User</span>
                                        <div class="span9">
                                            <?=form_input(array('name'=>'email', 'class'=>'span12', 'value'=>$email, 'placeholder'=>lang("user")));?>
                                        </div>
                                    </div>
                                    <div class="row-fluid">
                                        <span class="span3">Password</span>
                                        <div class="span9">
                                            <?=form_password(array('name'=>'password', 'class'=>'span12', 'value'=>$password, 'placeholder'=>lang("password")));?>
                                        </div>
                                    </div>
                                    <div class="row-fluid">
                                        <span class="span9">
                                            <?=form_checkbox(array('name'=>'remember', 'class'=>'uniform_on', 'value'=>'1', 'id'=>'remember', 'checked'=>$check))?>
                                            <label for="remember" style="display: inline"><?=lang("rememberme")?>?</label>
                                        </span>
                                        <div class="span3">
                                            <?=form_button(array('type'=>'submit', 'class'=>'btn-primary span12', 'content'=>lang('login')));?>
                                        </div>
                                    </div>
                                <?php echo form_close(); ?>
                                <?php echo form_open('login/resetPassword', 'id="reset", style="display:none;"');?>
                                    <div class="row-fluid">
                                        <span class="span3">User</span>
                                        <div class="span9">
                                            <?=form_input(array('name'=>'user', 'class'=>'span12', 'value'=>$email, 'placeholder'=>lang("user"), 'id'=>'user'));?>
                                        </div>
                                    </div>
                                    <div class="row-fluid">
                                        <span class="span3"></span>
                                        <div class="span9">
                                        </div>
                                    </div>
                                    <div class="row-fluid">
                                        <span class="span9">
                                        </span>
                                        <div class="span3">
                                            <?=form_button(array('type'=>'submit', 'class'=>'btn-primary span12', 'content'=>lang('send')));?>
                                        </div>
                                    </div>
                                <?php echo form_close(); ?>
                            </div>
                        </div>
                        
                        <div class="row-fluid" id="options-login">
                            <a href="#" class="span6 btn-primary gray">Register</a>
                            <a href="#" class="span6 btn-primary gray" id="forgot">Forgot password?</a>
                            <a href="#" class="span6 btn-primary gray" id="return" style="display: none">Return</a>
                        </div>

                        <div class="row-fluid" id="error-login">
                            <div class="span12"><?=$error?></div>
                        </div>
                        
                    </div>
                    
                </div>
                <br />
                <br />
            </div>
            <div class="span1 hidden-phone"> </div>
        </div>
        <!-- END CONTENT-->

