var Field;
var $view;
var generateFields;

fnc.require(['jQuery', 'Class', 'AJAXRequest'], function() {
	$view = $('#user_scores');
	
	Field = function(name, score) {
		var self = this;
		ClassWithBinds(self);
		
		this.__construct = function(name, score) {

			self.ressourcesPath		= 'ressources/images/';
			self.ressourcesEmpty	= 'empty_point.png';
			self.ressourcesHalf		= 'half_point.png';
			self.ressourcesFull		= 'full_point.png';
			
			self.ressourcesEmptyImg	= new Image();
			self.ressourcesEmptyImg.src = self.ressourcesPath + self.ressourcesEmpty;
			
			self.ressourcesHalfImg	= new Image();
			self.ressourcesHalfImg.src = self.ressourcesPath + self.ressourcesHalf;
			
			self.ressourcesFullImg	= new Image();
			self.ressourcesFullImg.src = self.ressourcesPath + self.ressourcesFull;
			
			
			
			self.$element = $(
				  '<tr>'
				+	'<th class="field_name"></th>'
				+	'<td class="field_score"></td>'
				+ '</tr>'
			);
			
			self.$name = self.$element.find('.field_name');
			self.$score = self.$element.find('.field_score');
			
			self.$scores = [];
			self.scoresTitles = ['Au secours !', 'Médiocre', 'Moyen', 'Bien', 'Parfait'];
			
			for(var i = 0; i < 5; i++) {
				self.$scores[i] =  $('<img class="score_point" src="" title="' + self.scoresTitles[i] + '">');
				self.$score.append(self.$scores[i]);
			}
			
			
			self.setName(name);
			self.setScore(score);
		}
		
		this.activateUserModification = function(useHalfPoint) {
			if(typeof useHalfPoint != "boolean") { var useHalfPoint = false; }
			
			self.$score.mouseleave(function() {
				self._displayScore(self.score);
			});
			
			for(var i = 0; i < 5; i++) {
				var $score = self.$scores[i];
				$score.mousemove(fnc.closure(function(event, i, $score) {
					if(useHalfPoint) {
						var score = i + 0.5;
						var x = event.clientX - $score.offset().left;
						var width = $score[0].offsetWidth;
						if(x > width / 2) { score += 0.5; }
					} else {
						var score = i + 1;
					}
					
					self.userScore = score;
					self._displayScore(score);
				}, [i, $score]));
				
				$score.click(fnc.closure(function(event, i) {
					self.trigger('score_change', [self.userScore]);
				}, [i]));
			}
		}
		
		this.setName = function(name) {
			self.name = name;
			self.$name.text(self.name);
			return this;
		}
		
		this.setScore = function(score) {
			self.score = score;
			self._displayScore(self.score);
			return this;
		}
		
		this._displayScore = function(score) {
			score = Math.round(score * 2) / 2;
			
			for(var i = 0; i < 5; i++) {
				var $score = self.$scores[i];
				if(score > i) {
					if((score - i) == 0.5) {
						$score.attr('src', self.ressourcesHalfImg.src);
					} else {
						$score.attr('src', self.ressourcesFullImg.src);	
					}
				} else {
					$score.attr('src', self.ressourcesEmptyImg.src);	
				}
			}
		}
			
		this.get$ = function() {
			return self.$element;
		}
		
		this.__construct(name, score);
	}
	

	generateFields = function(fields) {
		var $table = $view.find('.scores_fields');
		$table.empty();

		for(field_name in fields) {
			var field = fields[field_name];
			$table.append(field.get$());
			field.activateUserModification(true);
			field.bind('score_change', fnc.closure(function(score, field_name, field) {
				var req = new AJAXRequest({
					'url'	: 'views/user_scores/set_user_scores.php',
					'type'	: 'get',
					'data'	: {
						'score_fitness_id'	: fitness_id,
						'score_name'		: field_name,
						'score_value'		: score
					},
					'responseType'	: 'json'
				});
				
				req.bind('complete', function(response) {
					if(response.status == "OK") {
						field.setScore(score);
					} else {
						console.error(response.message);
					}
				});
			}, [field_name, field]));
		}
		
		
	}
	
	
	
	fnc.libReady('Field', Field);
});