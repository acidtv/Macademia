var app = app || {};

(function() {
	var PlayerModel = Backbone.Model.extend({
		songid: '',
		currentsong: {},
		status: {},

		initialize: function() {
			this.update();
		},

		update: function() {
			$.getJSON('/api/mpd/status', function(response) {
				self = app.PlayerModel;
				self.status = response.data;
				self.trigger('player:update', self);
				self.checkNewSong(response.data);
			});
		},

		checkNewSong: function (data) {
			songid = data.playlist + data.songid;

			if (songid != this.songid) {
				this.songid = songid;
				$.getJSON('/api/mpd/currentsong', function(response) {
					app.PlayerModel.setNewSong(response.data);
				});
			}
		},

		setNewSong: function (data) {
			console.log('newsong');
			this.currentsong = data;
			this.trigger('player:newsong', this);
		}
	});

	app.PlayerModel = new PlayerModel();
}());

(function() {
	app.PlaylistSongModel = Backbone.Model.extend({
		idAttribute: 'Id',

		markAsCurrentSong: function() {
			this.trigger('currentsong', this);
		}
	});
}());

(function() {
	var PlaylistCollection = Backbone.Collection.extend({
		model: app.PlaylistSongModel,
		url: '/api/mpd/playlistinfo',

		initialize: function() {
			this.listenTo(app.PlayerModel, 'player:newsong', this.markCurrentSong);
		},

		parse: function(response) {
			return _.values(response.data);
		},

		markCurrentSong: function(player) {
			this.get(player.currentsong.Id).markAsCurrentSong();
		}
	});

	app.PlaylistCollection = new PlaylistCollection();
}());

(function() {
	app.PlaylistView = Backbone.View.extend({
		el: '#playlist',

		initialize: function() {
			this.listenTo(app.PlaylistCollection, 'reset', this.addAll);
			app.PlaylistCollection.fetch();
		},

		render: function() {
			// playlist stats
		},

		addOne: function(item) {
			view = new app.PlaylistItemView({model: item});
			this.$el.append(view.render().el);
		},

		addAll: function() {
			app.PlaylistCollection.each(this.addOne, this)
		}
	});
}());

(function() {
	app.PlaylistItemView = Backbone.View.extend({
		tagName: 'tr',

		template: _.template($('#playlist-item').html()),

		events: {
			'dblclick': 'playThisSong'
		},

		initialize: function() {
			this.listenTo(this.model, 'currentsong', this.markAsCurrentSong);
		},

		test: function() {
			console.log(this.model);
		},

		render: function() {
			data = this.model.toJSON();
			data._Time = Math.floor(data.Time/60) + ':' + (data.Time%60);
			this.$el.html(this.template(data));
			return this;
		},

		playThisSong: function() {
			// app.PlayerModel.
		},

		markAsCurrentSong: function() {
			this.$el.siblings().removeClass('selected');
			this.$el.addClass('selected');
		}
	});
}());

(function() {
	app.AppView = Backbone.View.extend({
		el: '#player',

		events: {
			'click #start': 'startPlayback'
		},

		initialize: function() {
			this.$progressBar = this.$('#progress #bar');

			this.listenTo(app.PlayerModel, 'player:update', this.updateProgress);
			this.listenTo(app.PlayerModel, 'player:newsong', this.updateSong);

			// start progress updater
			setInterval(function() { app.PlayerModel.update(); }, 1000);
		},

		render: function() {
		},

		startPlayback: function() {
			alert('starting playback');
		},

		updateProgress: function(player) {
			this.$progressBar.width(player.status.elapsed);
		},

		updateSong: function(player) {
			this.$('#artist').html(player.currentsong.Artist);
			this.$('#song').html(player.currentsong.Title);
		}
	})

}());
