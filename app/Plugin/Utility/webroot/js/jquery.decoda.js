/**
 * Port from Decoda.js MooTools plugin by Miles Johnson as a jQuery plugin
 * @copyright   Copyright 2014, Hegesippe Espace <loveangel1337@gmail.com>
 * @license     http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * See original work from Miles Johnson at http://milesj.me/code/mootools/decoda
 *
 * Creates a something something to add a textarea editor with toolbar functionality
 *
 * @uses jQuery (tests made against 1.10.2 and 1.11.1)
 * @uses Rangy Inputs (https://github.com/timdown/rangyinputs)
 */
;
(function($, w) {
  /* Tidying function */
  var walk = function(string, replacements) {
    var result = string, key;
    for (key in replacements)
      result = result.replace(replacements[key], key);
    return result;
  }, tidy = {
    ' ': /[\xa0\u2002\u2003\u2009]/g,
    '*': /[\xb7]/g,
    '\'': /[\u2018\u2019]/g,
    '"': /[\u201c\u201d]/g,
    '...': /[\u2026]/g,
    '-': /[\u2013]/g,
    '--': /[\u2014]/g,
    '&raquo;': /[\ufffd]/g
  };

  /**
   * Tidies a string by removing some special chars
   * From MooTools code
   * @returns {String}
   */
  String.prototype.tidy = function() {
    walk(this, tidy);
    return this;
  };
  /* End tidying function */
  w.Decoda = function(el, providedOptions) {
    var $$ = this;
    /**
     * Default options
     * @type {Object}
     */
    $$.options = {
      /**
       * Default tag open char
       * @type {String}
       */
      open: '[',
      /**
       * Default tag close char
       * @type {String}
       */
      close: ']',
      /**
       * CSS namespace applied on the editor
       * @type {String}
       */
      namespace: '',
      /**
       * URL to which send AJAX queries for preview
       * Disables preview if left empty
       * @type {String}
       */
      previewUrl: '',
      /**
       * Max number of lines for the tindying process
       * @type {int}
       */
      maxNewLines: 3,
      /**
       * Do we need to submit the whole form for the preview or only the
       * textarea ?
       * @type {boolean}
       */
      submitFullFormOnPreview: false,
      /**
       * Callback on submit
       * @type {function|null}
       */
      onSubmit: null,
      /**
       * Callback on tag insert
       * @type {function|null}
       */
      onInsert: null,
      /**
       * Callback on init
       * @type {function|null}
       */
      onInitialize: null,
      /**
       * Callback on toolbar render
       * @type {function|null}
       */
      onRenderToolbar: null,
      /**
       * Callback on preview rendering
       * @type {function|null}
       */
      onRenderPreview: null,
      /**
       * Callback on help rendering
       * @type {function|null}
       */
      onRenderHelp: null
    };
    /**
     * Static strings used by Rangy Inputs for cursor placement after insert
     * @type {Object}
     */
    $$.collapse = {
      collapseToEnd: 'collapseToEnd',
      collapseToStart: 'collapseToStart',
      select: 'select'
    };
    /**
     * Full editor container
     * @type {jQuery}
     */
    $$.editor = null;
    /**
     * Full toolbar container
     * @type {jQuery}
     */
    $$.toolbar = null;
    /**
     * Textarea reference
     * @type {jQuery}
     */
    $$.textarea = null;
    /**
     * Form reference
     * @type {jQuery}
     */
    $$.form = null;
    /**
     * Textarea container
     * @type {jQuery}
     * @see $$.textarea
     */
    $$.container = null;
    /**
     * Preview container
     * @type {jQuery}
     */
    $$.preview = null;
    /**
     * Help container
     * @type {jQuery}
     */
    $$.help = null;
    /**
     * Loaded tags
     * @type {Array}
     */
    $$.tags = [];

    /**
     * Inits the plugin option and vars
     * @param {jQuery|DOMElement|String} el
     * @param {Object} options
     * @returns
     */
    $$.init = function(el, options) {
      $.extend(this.options, options);
      if (typeof el.jquery === 'undefined') { //When provided with a non jQuery element (like String or a raw JS DOM Element, we throws it through jQuery selector
        this.textarea = $(el);
      } else {
        this.textarea = el;
      }
      if (!this.textarea || this.textarea.length !== 1) {//Now, we don't want more than 1 element at a time per call, or it becomes impossible to know where to add the toolbar, or the text for that instance
        throw new Decoda.DecodaException('Too many elements');
      }
      if (this.textarea.prop('nodeName').toLowerCase() !== 'textarea') {
        throw new Decoda.DecodaException('Textarea expected to be a textarea, got ' + this.textarea.prop('nodeName'));
      }

      this.form = this.textarea.parents('form');

      this.editor = this.createElement('div', 'decoda-editor');
      this.toolbar = this.createElement('div', 'decoda-toolbars');
      this.container = this.createElement('div', 'decoda-textarea');
      this.preview = this.createElement('div', 'decoda-preview').hide();
      this.help = this.createElement('div', 'decoda-help').hide();
      this.help.data('render', false);

      this.textarea.wrap(this.container);
      this.container = this.textarea.parent();
      this.container.wrap(this.editor);
      this.editor = this.container.parent();

      this.container.before(this.toolbar).after(this.preview, this.help);
      if (typeof this.options.namespace === 'string' && this.options.namespace) {//Add namespace if we have a string there
        this.editor.addClass(this.options.namespace);
      }
      if ($.isFunction(this.options.onSubmit)) { //Add the submit callback if we have a function in there
        this.form.bind('submit', $.proxy(this.options.onSubmit, this));
      }
      if ($.isFunction(this.options.onInitialize)) { //Callback on init
        $.proxy(this.options.onInitialize, this)();
      }
    };
    /**
     *
     * @param {String} type
     * @param {String} klass
     * @param {String|Iterable} klass
     * @returns {jQuery}
     */
    $$.createElement = function(type, klass) {
      var div = $(document.createElement(type));
      if (typeof klass === 'string') { //Only one, provided as string
        div.addClass(klass);
      } else if (typeof klass === 'undefined') { //Not provided
      } else { //Provided as an Object or Array
        $.each(klass, $.proxy(function(index, kl) {
          if (typeof kl === 'string') { //Provided we have a string, let's assume it is a class
            this.addClass(kl);
          }
        }, div));
      }
      return div;
    };
    /**
     * Creates the default toolbar :
     * All commands from Decoda.controls
     * All filters from Decoda.filters except (email, url, image, video)
     * @param {Array} blacklist
     * @returns {Decoda}
     */
    $$.defaults = function(blacklist) {
      this.addFilters(null, null, blacklist);
      this.addControls(null, null, blacklist);

      return this;
    };
    /**
     * Add new Controls to the toolbar
     * @param {String} control
     * @param {Object} commands
     * @param {Array} blacklist
     * @returns {Decoda}
     */
    $$.addControls = function(control, commands, blacklist) {
      if (!commands) { //If not provided, add default ones
        $.each(Decoda.controls, $.proxy(function(control, commands) {
          this.addControls(control, commands, blacklist);
        }, this));
      } else {
        this.buildToolbar(control, commands, blacklist);
      }
      return this;
    };
    /**
     * Add new Filters to the toolbar
     * @param {String} filter
     * @param {Object} tags
     * @param {Array} blacklist
     * @returns {Decoda}
     */
    $$.addFilters = function(filter, tags, blacklist) {
      if (!tags) { //If not provided, add default ones
        var filters = $.extend({}, Decoda.filters);
        delete filters.email;
        delete filters.url;
        delete filters.image;
        delete filters.video;

        $.each(filters, $.proxy(function(filter, tags) {
          this.addFilters(filter, tags, blacklist);
        }, this));
      } else { //Push each tag through the creation process and add them to the loaded tags list
        this.buildToolbar(filter, tags, blacklist);
        $.each(tags, $.proxy(function(index, tag) {
          this.tags.push(tag);
        }, this));
      }
      return this;
    };
    /**
     * Builds the toolbar element from the structured object
     * @param {string} id
     * @param {Object} commands
     * @param {Array} blacklist
     * @returns {Decoda}
     */
    $$.buildToolbar = function(id, commands, blacklist) {
      blacklist = $.isArray(blacklist) ? blacklist : [];
      var ul = this.createElement('ul', ['decoda-toolbar', 'toolbar-' + id]), li, button, menu, anchor;
      $.each(commands, $.proxy(function(index, command) {
        if (blacklist.indexOf(command.tag) >= 0) { //Check against the blacklist
          return;
        }
        li = this.createElement('li');
        button = this.createElement('button', 'tag-' + command.tag).html('<span></span>').attr({
          type: 'button',
          title: command.title
        });
        //Bind the call on button pressing
        button.bind('click', $.proxy(command.onClick || this.insertTag, this, command, button));
        if (command.key) { //Bind the keydown event for keyboard shortcuts
          button.attr('title', button.attr('title') + ' (Ctrl + ' + command.key.toUpperCase() + ')');
          command.keyCode = command.key.toUpperCase().charCodeAt(0);
          this.textarea.bind('keydown', $.proxy(function(e) {
            this._listenKeydown(e, command, button);
          }, this));
        }
        if (command.className) { //Custom class for elements
          button.addClass(command.className);
        }
        li.append(button);

        if (command.options) { //Add a submenu if needed
          menu = this.createElement('ul', ['decoda-menu', 'menu-' + command.tag]);
          $.each(command.options, $.proxy(function(index, option) {
            option = $.extend({}, command, option);
            if (blacklist.indexOf(option.tag) >= 0) {
              return;
            }
            anchor = this.createElement('a').html('<span></span>' + option.title).attr('href', 'javascript:;').attr('title', option.title);
            anchor.bind('click', $.proxy(option.onClick || this.insertTag, this, option, anchor));
            if (option.className) {
              anchor.addClass(option.className);
            }
            menu.append(this.createElement('li').append(anchor));
          }, this));
          li.append(menu);
        }

        ul.append(li);

      }, this));
      if (ul[0].hasChildNodes()) { //If the ul is not empty, append it to the toolbar
        this.toolbar.append(ul);
        if (this.help.data('render')) {
          this.help.data('render', false);
        }
        if ($.isFunction(this.options.onRenderToolbar)) { //Callback on render
          $.proxy(this.options.onRenderToolbar, this, ul)();
        }
      }

      //Fixes zIndex
      var toolbars = this.toolbar.find('.decoda-toolbar'), z = toolbars.length;
      toolbars.each(function(index, toolbar) {
        $(toolbar).css('zIndex', z);
        z--;
      });
      return this;
    };
    /**
     * Disables the toolbar
     * @returns {Decoda}
     */
    $$.disableToolbar = function() {
      this.toolbar.find('button').each(function(index, node) {
        $(node).prop('disabled', true).parents('li').addClass('disabled');
      });
      return this;
    };
    /**
     * Enables the toolbar
     * @returns {Decoda}
     */
    $$.enableToolbar = function() {
      this.toolbar.find('button').each(function(index, node) {
        $(node).prop('disabled', false).parents('li').removeClass('disabled');
      });
      return this;
    };
    /**
     * Clean the text
     * Remove Unicode special chars from Word
     * Transforms specific new lines to unix type new lines
     * Ensures there is no more than maxNewLines new lines in a row
     * Trim the value
     * @returns {Decoda}
     */
    $$.clean = function() {
      var value = this.textarea.val(), max = this.options.maxNewLines;
      value = value.replace(/\r\n/g, "\n").replace(/\r/g, "\n");
      if (max) {
        value = value.replace(new RegExp("\n{" + (max + 1) + ",}", 'g'), this.repeatString("\n", max));
      }
      value = value.trim();
      value = value.tidy();
      this.textarea.val(value);
      return this;
    };
    /**
     * Repeat the str cnt times and returns the result
     * @param {String} str
     * @param {int} cnt
     * @returns {String}
     */
    $$.repeatString = function(str, cnt) {
      var res = '';
      for (cnt; cnt > 0; cnt--) {
        res += str;
      }
      return res;
    };
    /**
     * Inserts a tag
     * @param {Object} tag
     * @returns {Decoda}
     */
    $$.insertTag = function(tag) {
      this.textarea.focus();
      var defaultValue, contentValue = this.textarea.getSelection().text, field = tag.promptFor || 'default', answer;
      if (tag.prompt) {//If we have a prompt, ask for the value
        answer = prompt(tag.prompt);
        if (answer === null) {
          return this;
        }
        if ($.isFunction(tag.onInsert)) { //Call any custom function defined by the tag
          answer = $.proxy(tag.onInsert, this, answer, field)();
        }
        if (field === 'default') {
          defaultValue = answer;
        } else {
          contentValue = answer;
        }
      }
      //Generate the markup and insert it
      var markup = this.formatTag(tag, defaultValue, contentValue);
      if (this.textarea.getSelection().length > 0) {//We have a selection
        if (tag.selfClose) {
          this.textarea.replaceSelectedText(markup, this.collapse.collapseToEnd);
        } else {
          this.textarea.replaceSelectedText(markup);
        }
      } else { //We have no selection (selected text is 0 length, selection.start is the cursor pos
        this.textarea.replaceSelectedText(markup, this.collapse.collapseToEnd);
        if (!tag.selfClose) {//Place the cursor between start and end tags if needed
          var close = this.formatTag(tag, defaultValue, contentValue, 'close');
          this.textarea.setSelection(this.textarea.getSelection().start - close.length);
        }
      }
      if ($.isFunction(this.options.onInsert)) { //Callback on insert
        $.proxy(this.options.onInsert, this, markup)();
      }

      return this;
    };
    /**
     * Create the actual markup to be inserted
     * @param {Object} tag
     * @param {String} defaultValue
     * @param {String} contentValue
     * @param {String} type
     * @returns {String}
     */
    $$.formatTag = function(tag, defaultValue, contentValue, type) {
      defaultValue = defaultValue || tag.defaultValue || '';
      contentValue = contentValue || tag.placeholder || '';

      var t = tag.tag, o = this.options.open, c = this.options.close, open = o + t, close = o + '/' + t + c,
              field;
      if (tag.hasDefault) {
        field = tag.promptFor || 'default';
        if (tag.prompt && field === 'default') {
          if (defaultValue) {
            open += '="' + defaultValue + '"';
          }
        } else {
          open += '="' + defaultValue + '"';
        }
      }
      if (tag.selfClose) {
        return open + '/' + c;
      } else {
        open += c;
      }
      if (type === 'open') {
        return open;
      } else if (type === 'close') {
        return close;
      }
      return open + contentValue + close;
    };
    /**
     * Renders the help section
     * @returns {Decoda}
     */
    $$.renderHelp = function() {
      this.help.data('render', true);
      this.help.empty();//Clean this in case we are generating again
      var table = this.createElement('table'),
              thead = this.createElement('thead'),
              tbody = this.createElement('tbody'),
              tr, examples, attributes;

      tr = this.createElement('tr');
      tr.append(this.createElement('th').html('Tag'),
              this.createElement('th').html('Attributes'),
              this.createElement('th').html('Examples'));
      thead.append(tr);
      $.each(this.tags, $.proxy(function(index, tag) {
        attributes = (tag.attributes || []).join(', ');
        examples = (tag.examples || [this.formatTag(tag)]).join(', ');

        tr = this.createElement('tr');
        tr.append(
                this.createElement('td', 'tag-title').html(tag.title),
                this.createElement('td', 'tag-attributes').html(attributes),
                this.createElement('td', 'tag-examples').html(examples)
                );
        tbody.append(tr);
      }, this));
      ;
      this.help.append(table.append(thead, tbody));
      if ($.isFunction(this.options.onRenderHelp)) { //Callback on help render
        $.proxy(this.options.onRenderHelp, this, table)();
      }
      return this;
    };
    /**
     * Renders the preview section
     * @returns {Decoda}
     */
    $$.renderPreview = function() {
      this.preview.addClass('loading');
      var isSubmittedFullyAndWithFileEnctype = !!(this.options.submitFullFormOnPreview && (typeof this.form.attr('enctype') !== 'undefined' && this.form.attr('enctype') === 'multipart/form-data'));
      $.ajax({
        url: this.options.previewUrl,
        type: 'post',
        data: (this.options.submitFullFormOnPreview) ? (isSubmittedFullyAndWithFileEnctype ? new FormData(this.form[0]) : this.form.serialize()) : {
          input: this.textarea.val()
        },
        processData: !isSubmittedFullyAndWithFileEnctype, //Needed so that jQuery does not parse FormData and fails
        contentType: isSubmittedFullyAndWithFileEnctype ? false : 'application/x-www-form-urlencoded; charset=UTF-8', //FormData needs no contentType because of the multipart
        success: $.proxy(function(response) {
          this.preview.removeClass('loading').html(response);
        }, this),
        error: $.proxy(function(xhr, status, error) {
          this.container.show();
          this.enableToolbar();
          this.preview.removeClass('visible').hide();
          alert('An error has occured while rendering the preview. Error : ' + error);
        }, this)
      });

      if ($.isFunction(this.options.onRenderPreview)) { //Callback on preview render
        $.proxy(this.options.onRenderPreview, this)();
      }
      return this;
    };
    /**
     * Keydown listener for shortcuts
     * @param {KeyEvent} e
     * @param {Object} command
     * @param {DOMElement} button
     * @returns {Decoda}
     */
    $$._listenKeydown = function(e, command, button) {
      if (e.ctrlKey && !e.altKey && ((typeof (e.key) !== 'undefined' && e.key === command.key) || (e.keyCode === command.keyCode))) {
        e.stopPropagation();
        e.preventDefault();
        if ($.isFunction(command.onClick)) {
          $.proxy(command.onClick, this, command, button)();
        } else {
          this.insertTag(command, button);
        }
        return false;
      }
    };
    $.proxy(this.init, this, el, providedOptions)();
    return this;
  };
  /**
   * Decoda Filters
   * @type Object
   */
  w.Decoda.filters = {
    defaults: {
      b: {tag: 'b', title: 'Bold', key: 'b'},
      i: {tag: 'i', title: 'Italics', key: 'i'},
      u: {tag: 'u', title: 'Underline', key: 'u'},
      s: {tag: 's', title: 'Strike-Through', key: 's'},
      sub: {tag: 'sub', title: 'Subscript'},
      sup: {tag: 'sup', title: 'Superscript'},
      abbr: {
        tag: 'abbr',
        title: 'Abbreviation',
        hasDefault: true,
        prompt: 'Title:',
        attributes: ['default'],
        examples: ['[abbr="Hyper-Text Markup Language"]HTML[/abbr]']
      },
      time: {
        tag: 'time',
        title: 'Timestamp',
        prompt: 'Date:',
        promptFor: 'content'
      },
      br: {tag: 'br', title: 'Line Break', selfClose: true},
      hr: {tag: 'hr', title: 'Horizontal Break', selfClose: true}
    },
    text: {
      font: {
        tag: 'font',
        title: 'Font Family',
        prompt: 'Font:',
        hasDefault: true,
        attributes: ['default'],
        examples: ['[font="Arial"][/font]'],
        options: [
          {title: 'Arial', defaultValue: 'Arial', className: 'font-arial', prompt: false},
          {title: 'Tahoma', defaultValue: 'Tahoma', className: 'font-tahoma', prompt: false},
          {title: 'Verdana', defaultValue: 'Verdana', className: 'font-verdana', prompt: false},
          {title: 'Courier', defaultValue: 'Courier', className: 'font-courier', prompt: false},
          {title: 'Times', defaultValue: 'Times', className: 'font-times', prompt: false},
          {title: 'Helvetica', defaultValue: 'Helvetica', className: 'font-helvetica', prompt: false}
        ]
      },
      size: {
        tag: 'size',
        title: 'Text Size',
        prompt: 'Size:',
        hasDefault: true,
        attributes: ['default'],
        examples: ['[size="12"][/size]'],
        options: [
          {title: 'Small', defaultValue: '10', className: 'size-small', prompt: false},
          {title: 'Normal', defaultValue: '12', className: 'size-normal', prompt: false},
          {title: 'Medium', defaultValue: '18', className: 'size-medium', prompt: false},
          {title: 'Large', defaultValue: '24', className: 'size-large', prompt: false}
        ],
        onInsert: function(value, field) {
          if (field === 'default') {
            var res = Number(value);
            if (!isNan(res)) {
              return res < 10 ? 10 : ((res > 29) ? 29 : res);
            }
            return 12;
          }
          return value;
        }
      },
      color: {
        tag: 'color',
        title: 'Text Color',
        prompt: 'Hex Code:',
        hasDefault: true,
        attributes: ['default'],
        examples: ['[color="red"][/color]'],
        options: [
          {title: 'Yellow', defaultValue: 'yellow', className: 'color-yellow', prompt: false},
          {title: 'Orange', defaultValue: 'orange', className: 'color-orange', prompt: false},
          {title: 'Red', defaultValue: 'red', className: 'color-red', prompt: false},
          {title: 'Blue', defaultValue: 'blue', className: 'color-blue', prompt: false},
          {title: 'Purple', defaultValue: 'purple', className: 'color-purple', prompt: false},
          {title: 'Green', defaultValue: 'green', className: 'color-green', prompt: false},
          {title: 'White', defaultValue: 'white', className: 'color-white', prompt: false},
          {title: 'Gray', defaultValue: 'gray', className: 'color-gray', prompt: false},
          {title: 'Black', defaultValue: 'black', className: 'color-black', prompt: false}
        ],
        onInsert: function(value, field) {
          if (field === 'default') {
            return (/(?:#[0-9a-f]{3,6}|[a-z]+)/i).exec(value) ? value : null;
          }

          return value;
        }
      },
      heading: {
        tag: 'h1',
        title: 'Heading',
        examples: ['[h1][/h1], [h2][/h2], [h3][/h3], [h4][/h4], [h5][/h5], [h6][/h6]'],
        options: [
          {tag: 'h1', title: '1st', className: 'heading-h1'},
          {tag: 'h2', title: '2nd', className: 'heading-h2'},
          {tag: 'h3', title: '3rd', className: 'heading-h3'},
          {tag: 'h4', title: '4th', className: 'heading-h4'},
          {tag: 'h5', title: '5th', className: 'heading-h5'},
          {tag: 'h6', title: '6th', className: 'heading-h6'}
        ]
      }
    },
    block: {
      left: {tag: 'left', title: 'Left Align'},
      center: {tag: 'center', title: 'Center Align'},
      right: {tag: 'right', title: 'Right Align'},
      justify: {tag: 'justify', title: 'Justify Align'},
      hide: {tag: 'hide', title: 'Hide'},
      spoiler: {tag: 'spoiler', title: 'Spoiler'}
    },
    list: {
      list: {
        tag: 'list',
        title: 'Unordered List',
        examples: ['[list][/list]', '[list="upper-alpha"][/list]'],
        attributes: ['default <span>(optional)</span>']
      },
      olist: {
        tag: 'olist',
        title: 'Ordered List',
        examples: ['[olist][/olist]', '[olist="lower-roman"][/olist]'],
        attributes: ['default <span>(optional)</span>']
      },
      li: {
        tag: 'li',
        title: 'List Item'
      }
    },
    quote: {
      quote: {
        tag: 'quote',
        title: 'Quote Block',
        prompt: 'Author:',
        hasDefault: true,
        examples: ['[quote][/quote]', '[quote="Author"][/quote]', '[quote date="12/12/2012"][/quote]'],
        attributes: ['default <span>(optional)</span>', 'date <span>(optional)</span>']
      }
    },
    code: {
      code: {
        tag: 'code',
        title: 'Code Block',
        examples: ['[code][/code]', '[code="html"][/code]', '[code hl="1,5,10"][/code]'],
        attributes: ['default <span>(optional)</span>', 'hl <span>(optional)</span>']
      },
      source: {
        tag: 'source',
        title: 'Code Snippet'
      },
      'var': {
        tag: 'var',
        title: 'Variable'
      }
    },
    email: {
      email: {
        tag: 'email',
        title: 'Email',
        prompt: 'Email Address:',
        hasDefault: true,
        examples: ['[email]email@domain.com[/email]', '[email="email@domain.com"][/email]'],
        attributes: ['default <span>(optional)</span>']
      }
    },
    url: {
      url: {
        tag: 'url',
        title: 'URL',
        prompt: 'Web Address:',
        hasDefault: true,
        examples: ['[url]http://domain.com[/url]', '[url="http://domain.com"][/url]'],
        attributes: ['default <span>(optional)</span>']
      }
    },
    image: {
      img: {
        tag: 'img',
        title: 'Image',
        prompt: 'Image URL:',
        promptFor: 'content',
        examples: ['[img][/img]', '[img="200x200"][/img]', '[img width="250" height="15%"][/img]'],
        attributes: ['default <span>(optional)</span>', 'width <span>(optional)</span>', 'height <span>(optional)</span>', 'alt <span>(optional)</span>']
      }
    },
    video: {
      video: {
        tag: 'video',
        title: 'Video',
        prompt: 'Video ID:',
        promptFor: 'content',
        hasDefault: true,
        examples: ['[video="youtube"]ID[/video]', '[youtube size="large"]ID[/youtube]', '[veoh size="small"]ID[/veoh]'],
        attributes: ['default', 'size <span>(optional)</span>'],
        options: [
          {tag: 'youtube', title: 'YouTube', hasDefault: false, className: 'video-youtube'},
          {tag: 'vimeo', title: 'Vimeo', hasDefault: false, className: 'video-vimeo'},
          {tag: 'veoh', title: 'Veoh', hasDefault: false, className: 'video-veoh'},
          {tag: 'vevo', title: 'Vevo', hasDefault: false, className: 'video-vevo'},
          {tag: 'liveleak', title: 'LiveLeak', hasDefault: false, className: 'video-liveleak'},
          {tag: 'dailymotion', title: 'Daily Motion', hasDefault: false, className: 'video-dailymotion'},
          {tag: 'funnyordie', title: 'Funny or Die', hasDefault: false, className: 'video-funnyordie'},
          {tag: 'collegehumor', title: 'College Humor', hasDefault: false, className: 'video-collegehumor'},
          {tag: 'myspace', title: 'MySpace', hasDefault: false, className: 'video-myspace'},
          {tag: 'wegame', title: 'WeGame', hasDefault: false, className: 'video-wegame'}
        ]
      }
    },
    table: {
      table: {
        tag: 'table',
        title: 'Table',
        examples: ['[table][/table]', '[table="sortable"][/table] <span>(class)</span>'],
        attributes: ['default <span>(optional)</span>']
      },
      thead: {tag: 'thead', title: 'Table Head'},
      tbody: {tag: 'tbody', title: 'Table Body'},
      tfoot: {tag: 'tfoot', title: 'Table Foot'},
      tr: {tag: 'tr', title: 'Table Row'},
      td: {
        tag: 'td',
        title: 'Table Cell',
        examples: ['[td][/td]', '[td="3"][/td] <span>(colspan)</span>'],
        attributes: ['default <span>(optional)</span>']
      },
      th: {
        tag: 'th',
        title: 'Table Header',
        examples: ['[th][/th]', '[th="3"][/th] <span>(colspan)</span>'],
        attributes: ['default <span>(optional)</span>']
      }
    }
  };
  /**
   * Decoda Controls
   * @type Object
   */
  w.Decoda.controls = {
    editor: {
      preview: {
        tag: 'preview',
        key: 'e',
        title: 'Preview',
        onClick: function(command, button) {
          if (!this.options.previewUrl) {
            alert('Preview functionality has not been enabled');
            return;
          }

          this.container.hide();
          this.help.hide();

          if (this.preview.hasClass('visible')) { //Trick to know if the div is showing or now
            this.preview.removeClass('visible').hide().empty();
            this.container.show();
            this.enableToolbar();

          } else {
            this.preview.addClass('visible').show();
            this.disableToolbar();
            this.renderPreview();
          }

          button.prop('disabled', false);
        }
      },
      clean: {
        tag: 'clean',
        title: 'Clean',
        onClick: function(command, button) {
          this.disableToolbar();
          if (this.clean()) {
            window.setTimeout($.proxy(function() {
              this.enableToolbar();
            }, this), 500);
          }
        }
      },
      help: {
        tag: 'help',
        title: 'Help',
        onClick: function(command, button) {
          if (!this.tags.length) {
            alert('No tag filters have been loaded');
            return;
          }

          this.container.hide();
          this.preview.hide();

          if (!this.help.data('render')) { //Trick to know if the help has previously been rendered
            this.renderHelp();
          }

          if (this.help.hasClass('visible')) {
            this.help.removeClass('visible').hide();
            this.container.show();
            this.enableToolbar();
          } else {
            this.help.addClass('visible').show();
            this.disableToolbar();
          }
          button.prop('disabled', false);
        }
      }
    }
  };
  w.Decoda.DecodaException = function(message) {
    this.message = message;
  }
  /**
   * jQuery plugin entry point
   * Called by $(el).Decoda({})
   * @param {Object} options
   * @returns {Decoda}
   */
  $.fn.decoda = function(options) {
    var $this=this;
    if (typeof this === 'string' || (!this.jquery && this.nodeType === 1)) {
      //If we get a string, it must be a jQuery selector.
      //If we get a DOMElement, it is wrapped in jQuery beforehand
      $this = $(this);
    }
    if ((!!$this.jquery && $this.length > 1) || $.isArray($this)) {
      var rVal = [];
      //Multiple elements
      if (!!$this.jquery) {
        //jQuery object
        $this.each(function(index, val) { //Iterate over everything
          var id = val.id || 'decoda-id-' + (new Date()).getTime() + '-' + Math.floor((1 + 10000 * Math.random()));
          rVal[id] = new Decoda($(val).attr('id', id), options);
        });
      } else {
        var id;
        //Array of elements
        for (el in $this) {
          e = $this[el];
          if (e.nodeType === 1) {
            id = e.id || 'decoda-id-' + (new Date()).getTime() + '-' + Math.floor((1 + 10000 * Math.random()));
            rVal[id] = new Decoda($(e).attr('id', id), options);
          }
        }
      }
      return rVal;
    } else {
      //Single element
      return new Decoda($this, options);
    }
  };
})(jQuery, window);