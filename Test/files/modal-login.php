<div class="modal fade modal-login modal-border-transparent" id="loginModal" tabindex="-1" role="dialog" aria-hidden="true" >
		<div class="modal-dialog">
			<div class="modal-content">
				
				<button type="button" class="btn btn-close close" data-dismiss="modal" aria-label="Close">
					<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
				</button>
				
				<div class="clear"></div>
				
				<!-- Begin # DIV Form -->
				<div id="modal-login-form-wrapper">
					
					<!-- Begin # Login Form -->
					<form id="login-form">
					
						<div class="modal-body pb-10">

							<div class="modal-seperator mb-40">
								<span>Connection</span>
							</div>
							
							<div class="form-group mb-0"> 
								<input id="login_username" class="form-control mb-5" placeholder="Utilisateur" type="text"> 
							</div>
							<div class="form-group mb-0"> 
								<input id="login_password" class="form-control mb-5" placeholder="Mot de passe" type="password"> 
							</div>
			
							<div class="form-group mb-0 mt-10">
								<div class="row gap-5">
									<div class="col-xs-12 col-sm-12 col-md-12 text-right"> 
										<button id="login_lost_btn" type="button" class="btn btn-link">Mot de passe oublié ?</button>
									</div>
								</div>
							</div>
						
						</div>
						
						<div class="modal-footer pt-25 pb-5">
						
							<div class="row gap-10">
								<div class="col-xs-6 col-sm-6 mb-10">
									<button type="submit" class="btn btn-primary btn-block">Connection</button>
								</div>
								<div class="col-xs-6 col-sm-6 mb-10">
									<button type="submit" class="btn btn-danger btn-block" data-dismiss="modal" aria-label="Close">Annuler</button>
								</div>
							</div>
							<div class="text-center">
								Vous avez pas encore de compte? 
								<button id="login_register_btn" type="button" class="btn btn-link">Inscription</button>
							</div>
							
						</div>
					</form>
					<!-- End # Login Form -->
								
					<!-- Begin | Lost Password Form -->
					<form id="lost-form" style="display:none;">
						<div class="modal-body pb-10">
							
							<div class="modal-seperator mb-40">
								<span>Mot de passe oublié</span>
							</div>
							
							<div class="form-group mb-0"> 
								<input id="lost_email" class="form-control mb-5" type="text" placeholder="Votre E-mail">
							</div>
							
							<div class="text-center">
								<button id="lost_login_btn" type="button" class="btn btn-link">Connection</button> ou 
								<button id="lost_register_btn" type="button" class="btn btn-link">Inscription</button>
							</div>
							
						</div>
						
						<div class="modal-footer pt-25 pb-5">
							
							<div class="row gap-10">
								<div class="col-xs-6 col-sm-6 mb-10">
									<button type="submit" class="btn btn-primary btn-block">Envoyer</button>
								</div>
								<div class="col-xs-6 col-sm-6 mb-10">
									<button type="submit" class="btn btn-danger btn-block" data-dismiss="modal" aria-label="Close">Annuler</button>
								</div>
							</div>
							
						</div>
						
					</form>
					<!-- End | Lost Password Form -->
								
					<!-- Begin | Register Form -->
					<form id="register-form" style="display:none;">
					
						<div class="modal-body pb-20">

							<div class="modal-seperator mb-40">
								<span>Inscription</span>
							</div>
							
							<div class="form-group mb-0"> 
								<input id="register_username" class="form-control mb-5" type="text" placeholder="Utilisateur"> 
							</div>
							
							<div class="form-group mb-0"> 
								<input id="register_email" class="form-control mb-5" type="email" placeholder="Email">
							</div>
							
							<div class="form-group mb-0"> 
								<input id="register_password" class="form-control mb-5" type="password" placeholder="Mot de Passe">
							</div>
							
							<div class="form-group mb-0"> 
								<input id="register_password_confirm" class="form-control mb-5" type="password" placeholder="Confirmez Votre Mot de Passe">
							</div>

						</div>
							
						<div class="modal-footer pt-25 pb-5">
						
							<div class="row gap-10">
								<div class="col-xs-6 col-sm-6 mb-10">
									<button type="submit" class="btn btn-primary btn-block">Inscription</button>
								</div>
								<div class="col-xs-6 col-sm-6 mb-10">
									<button type="submit" class="btn btn-danger btn-block" data-dismiss="modal" aria-label="Close">Annuler</button>
								</div>
							</div>
							
							<div class="text-center">
									Vous avez déjà un compte? <button id="register_login_btn" type="button" class="btn btn-link">Connection</button>
							</div>
							
						</div>
							
					</form>
					<!-- End | Register Form -->
								
				</div>
				<!-- End # DIV Form -->
								
			</div>
		</div>
	</div>