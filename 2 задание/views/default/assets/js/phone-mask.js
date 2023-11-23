!function(e) {
  "function" == typeof define && define.amd ? define([ "jquery" ], e) : e("object" == typeof exports ? require("jquery") : jQuery);
}(function(e) {
  var n, t = navigator.userAgent, a = /iphone/i.test(t), i = /chrome/i.test(t), r = /android/i.test(t);
  e.mask = {
      definitions: {
          9: "[0-9]",
          a: "[A-Za-z]",
          "*": "[A-Za-z0-9]"
      },
      autoclear: !0,
      dataName: "rawMaskFn",
      placeholder: "_"
  }, e.fn.extend({
      caret: function(e, n) {
          var t;
          if (0 !== this.length && !this.is(":hidden")) return "number" == typeof e ? (n = "number" == typeof n ? n : e, 
          this.each(function() {
              this.setSelectionRange ? this.setSelectionRange(e, n) : this.createTextRange && ((t = this.createTextRange()).collapse(!0), 
              t.moveEnd("character", n), t.moveStart("character", e), t.select());
          })) : (this[0].setSelectionRange ? (e = this[0].selectionStart, n = this[0].selectionEnd) : document.selection && document.selection.createRange && (n = (e = 0 - (t = document.selection.createRange()).duplicate().moveStart("character", -1e5)) + t.text.length), 
          {
              begin: e,
              end: n
          });
      },
      unmask: function() {
          return this.trigger("unmask");
      },
      mask: function(t, o) {
          var c, l, f, u, s, h, g, m;
          if (!t && this.length > 0) {
              var d = (c = e(this[0])).data(e.mask.dataName);
              return d ? d() : void 0;
          }
          return o = e.extend({
              autoclear: e.mask.autoclear,
              placeholder: e.mask.placeholder,
              completed: null
          }, o), l = e.mask.definitions, f = [], u = g = t.length, s = null, e.each(t.split(""), function(e, n) {
              "?" == n ? (g--, u = e) : l[n] ? (f.push(RegExp(l[n])), null === s && (s = f.length - 1), 
              u > e && (h = f.length - 1)) : f.push(null);
          }), this.trigger("unmask").each(function() {
              function c() {
                  if (o.completed) {
                      for (var e = s; h >= e; e++) if (f[e] && j[e] === d(e)) return;
                      o.completed.call(_);
                  }
              }
              function d(e) {
                  return o.placeholder.charAt(e < o.placeholder.length ? e : 0);
              }
              function p(e) {
                  for (;++e < g && !f[e]; ) ;
                  return e;
              }
              function $(e, n) {
                  var t, a;
                  if (!(0 > e)) {
                      for (t = e, a = p(n); g > t; t++) if (f[t]) {
                          if (!(g > a && f[t].test(j[a]))) break;
                          j[t] = j[a], j[a] = d(a), a = p(a);
                      }
                      k(), _.caret(Math.max(s, e));
                  }
              }
              function v() {
                  y(), _.val() != R && _.change();
              }
              function b(e, n) {
                  var t;
                  for (t = e; n > t && g > t; t++) f[t] && (j[t] = d(t));
              }
              function k() {
                  _.val(j.join(""));
              }
              function y(e) {
                  var n, t, a, i = _.val(), r = -1;
                  for (n = 0, a = 0; g > n; n++) if (f[n]) {
                      for (j[n] = d(n); a++ < i.length; ) if (t = i.charAt(a - 1), f[n].test(t)) {
                          j[n] = t, r = n;
                          break;
                      }
                      if (a > i.length) {
                          b(n + 1, g);
                          break;
                      }
                  } else j[n] === i.charAt(a) && a++, u > n && (r = n);
                  return e ? k() : u > r + 1 ? o.autoclear || j.join("") === x ? (_.val() && _.val(""), 
                  b(0, g)) : k() : (k(), _.val(_.val().substring(0, r + 1))), u ? n : s;
              }
              var _ = e(this), j = e.map(t.split(""), function(e, n) {
                  return "?" != e ? l[e] ? d(n) : e : void 0;
              }), x = j.join(""), R = _.val();
              _.data(e.mask.dataName, function() {
                  return e.map(j, function(e, n) {
                      return f[n] && e != d(n) ? e : null;
                  }).join("");
              }), _.one("unmask", function() {
                  _.off(".mask").removeData(e.mask.dataName);
              }).on("focus.mask", function() {
                  if (!_.prop("readonly")) {
                      var e;
                      clearTimeout(n), R = _.val(), e = y(), n = setTimeout(function() {
                          _.get(0) === document.activeElement && (k(), e == t.replace("?", "").length ? _.caret(0, e) : _.caret(e));
                      }, 10);
                  }
              }).on("blur.mask", v).on("keydown.mask", function e(n) {
                  if (!_.prop("readonly")) {
                      var t, i, r, o = n.which || n.keyCode;
                      m = _.val(), 8 === o || 46 === o || a && 127 === o ? (i = (t = _.caret()).begin, 
                      (r = t.end) - i == 0 && (i = 46 !== o ? function e(n) {
                          for (;--n >= 0 && !f[n]; ) ;
                          return n;
                      }(i) : r = p(i - 1), r = 46 === o ? p(r) : r), b(i, r), $(i, r - 1), n.preventDefault()) : 13 === o ? v.call(this, n) : 27 === o && (_.val(R), 
                      _.caret(0, y()), n.preventDefault());
                  }
              }).on("keypress.mask", function n(t) {
                  if (!_.prop("readonly")) {
                      var a, i, o, l = t.which || t.keyCode, u = _.caret();
                      !(t.ctrlKey || t.altKey || t.metaKey || 32 > l) && l && 13 !== l && (u.end - u.begin != 0 && (b(u.begin, u.end), 
                      $(u.begin, u.end - 1)), g > (a = p(u.begin - 1)) && (i = String.fromCharCode(l), 
                      f[a].test(i)) && ((function e(n) {
                          var t, a, i, r;
                          for (t = n, a = d(n); g > t; t++) if (f[t]) {
                              if (i = p(t), r = j[t], j[t] = a, !(g > i && f[i].test(r))) break;
                              a = r;
                          }
                      }(a), j[a] = i, k(), o = p(a), r) ? setTimeout(function() {
                          e.proxy(e.fn.caret, _, o)();
                      }, 0) : _.caret(o), u.begin <= h && c()), t.preventDefault());
                  }
              }).on("input.mask paste.mask", function() {
                  _.prop("readonly") || setTimeout(function() {
                      var e = y(!0);
                      _.caret(e), c();
                  }, 0);
              }), i && r && _.off("input.mask").on("input.mask", function e() {
                  var n = _.val(), t = _.caret();
                  if (m && m.length && m.length > n.length) {
                      for (y(!0); t.begin > 0 && !f[t.begin - 1]; ) t.begin--;
                      if (0 === t.begin) for (;t.begin < s && !f[t.begin]; ) t.begin++;
                      _.caret(t.begin, t.begin);
                  } else {
                      for (y(!0); t.begin < g && !f[t.begin]; ) t.begin++;
                      _.caret(t.begin, t.begin);
                  }
                  c();
              }), y();
          });
      }
  });
});