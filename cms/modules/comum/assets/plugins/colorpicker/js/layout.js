(function($){
	var initLayout = function() {
		
		$('.colorSelector').each(function(){
			$(this).find('input').each(function(){
				$(this).wrap('<div style="background-color:'+$(this).val() +'">');
			})
		})

		
		
		$('#picker_link_menu').ColorPicker({
			onShow: function (colpkr) {
				$(colpkr).fadeIn(500);
				return false;
			},
			onHide: function (colpkr) {
				$(colpkr).fadeOut(500);
				return false;
			},
			onChange: function (hsb, hex, rgb) {
				$('#picker_link_menu input').val('#' + hex);
				$('#picker_link_menu div').css('backgroundColor', '#' + hex);
			}
		});
		$('#picker_link_menu_hover').ColorPicker({
			onShow: function (colpkr) {
				$(colpkr).fadeIn(500);
				return false;
			},
			onHide: function (colpkr) {
				$(colpkr).fadeOut(500);
				return false;
			},
			onChange: function (hsb, hex, rgb) {
				$('#picker_link_menu_hover input').val('#' + hex);
				$('#picker_link_menu_hover div').css('backgroundColor', '#' + hex);
			}
		});
		$('#picker_titulo').ColorPicker({
			onShow: function (colpkr) {
				$(colpkr).fadeIn(500);
				return false;
			},
			onHide: function (colpkr) {
				$(colpkr).fadeOut(500);
				return false;
			},
			onChange: function (hsb, hex, rgb) {
				$('#picker_titulo input').val('#' + hex);
				$('#picker_titulo div').css('backgroundColor', '#' + hex);
			}
		});
		$('#picker_fundo_form').ColorPicker({
			onShow: function (colpkr) {
				$(colpkr).fadeIn(500);
				return false;
			},
			onHide: function (colpkr) {
				$(colpkr).fadeOut(500);
				return false;
			},
			onChange: function (hsb, hex, rgb) {
				$('#picker_fundo_form input').val('#' + hex);
				$('#picker_fundo_form div').css('backgroundColor', '#' + hex);
			}
		});
		
		
		$('#picker_cor_fundo_content').ColorPicker({
			onShow: function (colpkr) {
				$(colpkr).fadeIn(500);
				return false;
			},
			onHide: function (colpkr) {
				$(colpkr).fadeOut(500);
				return false;
			},
			onChange: function (hsb, hex, rgb) {
				$('#picker_cor_fundo_content input').val('#' + hex);
				$('#picker_cor_fundo_content div').css('backgroundColor', '#' + hex);
			}
		});
		
		$('#picker_cor_texto').ColorPicker({
			onShow: function (colpkr) {
				$(colpkr).fadeIn(500);
				return false;
			},
			onHide: function (colpkr) {
				$(colpkr).fadeOut(500);
				return false;
			},
			onChange: function (hsb, hex, rgb) {
				$('#picker_cor_texto input').val('#' + hex);
				$('#picker_cor_texto div').css('backgroundColor', '#' + hex);
			}
		});
		$('#picker_cor_fundo_topo').ColorPicker({
			onShow: function (colpkr) {
				$(colpkr).fadeIn(500);
				return false;
			},
			onHide: function (colpkr) {
				$(colpkr).fadeOut(500);
				return false;
			},
			onChange: function (hsb, hex, rgb) {
				$('#picker_cor_fundo_topo input').val('#' + hex);
				$('#picker_cor_fundo_topo div').css('backgroundColor', '#' + hex);
			}
		});
		
		
		
		
		
		
		
		
	};
	
	
	
	EYE.register(initLayout, 'init');
})(jQuery)