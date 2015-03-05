fnc.require(['Math'], function() {

	ProgressBar = function(container) {
		var self = this;
		
		if(typeof container == 'undefined') { var container = null; }
		
		this.init = function(container) {
			
			self.value = 0;
			self.container = null;
			
			self.progressBar = document.createElement('div');
				self.progressBar.classList.add('progressBar');
			
			self.progress = document.createElement('div');
				self.progress.classList.add('progress');
				self.progressBar.appendChild(self.progress);
				
			self.percent = document.createElement('div');
				self.percent.classList.add('percent');
				self.progress.appendChild(self.percent);
				
			self.overlay = document.createElement('div');
				self.overlay.classList.add('overlay');
				self.progressBar.appendChild(self.overlay);
				
			self.template = "";
			self.template += '	<div class="progressBar">';
			self.template += '		<div class="progress">';
			self.template += '			<div class="percent left">100%</div>';
			self.template += '		</div>';
			self.template += '		<div class="overlay"></div>';
			self.template += '	</div>';
			
			if(container) {
				self.append(container);
			}
			
			self.setValue(0);
		}
		
		this.append = function(container) {
			self.container = container;
			container.appendChild(self.progressBar);
		}
		
		this.remove = function() {
			self.container.removeChild(self.progressBar);
		}
		
		this.setValue = function(value) {
			self.value = Math.interval(value, 0, 1);
			
			var percent = Math.floor(self.value * 100) + '%';
			self.progress.style.width = percent;
			
			self.percent.innerHTML = percent;
			self.percent.classList.remove('left');
			self.percent.classList.remove('right');
			
			var progressOffsetWidth = self.progress.offsetWidth;
			var percentOffsetWidth = self.percent.offsetWidth;
			
			if(progressOffsetWidth >= percentOffsetWidth + 4) {
				self.percent.classList.add('left');
			} else {
				self.percent.classList.add('right');
			}
		}
		
		this.getValue = function() {
			return self.value;
		}
		
		this.init(container);
	}

	fnc.libs['GUI/ProgressBar'] = ProgressBar;
});