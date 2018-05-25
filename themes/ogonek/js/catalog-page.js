(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
var CardModel, CatalogPage,
  extend = function(child, parent) { for (var key in parent) { if (hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; },
  hasProp = {}.hasOwnProperty;

CardModel = require('./models/card.coffee');

CatalogPage = (function(superClass) {
  extend(CatalogPage, superClass);

  function CatalogPage() {
    return CatalogPage.__super__.constructor.apply(this, arguments);
  }

  CatalogPage.prototype.initialize = function() {
    return console.log('Yeeee!');
  };

  return CatalogPage;

})(Backbone.View);

},{"./models/card.coffee":2}],2:[function(require,module,exports){
var CardModel,
  extend = function(child, parent) { for (var key in parent) { if (hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; },
  hasProp = {}.hasOwnProperty;

CardModel = (function(superClass) {
  extend(CardModel, superClass);

  function CardModel() {
    return CardModel.__super__.constructor.apply(this, arguments);
  }

  CardModel.prototype.initialize = function() {
    return this.name = 'Asss';
  };

  return CardModel;

})(Backbone.Model);

},{}]},{},[1]);
