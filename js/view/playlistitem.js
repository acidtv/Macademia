var app = app || {};

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

