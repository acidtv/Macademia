var app = app || {};

(function() {
	app.PlaylistSongModel = Backbone.Model.extend({
		idAttribute: 'Id',

		markAsCurrentSong: function() {
			this.trigger('currentsong', this);
		}
	});
}());
