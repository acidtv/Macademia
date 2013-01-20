var app = app || {};

(function() {
	app.PlaylistView = Backbone.View.extend({
		el: '#playlist',

		initialize: function() {
			this.listenTo(app.PlaylistCollection, 'reset', this.addAll);
		},

		render: function() {
			// playlist stats
		},

		addOne: function(item) {
			view = new app.PlaylistItemView({model: item});
			this.$el.append(view.render().el);
		},

		addAll: function() {
			this.$el.empty();
			app.PlaylistCollection.each(this.addOne, this)
		}
	});
}());
