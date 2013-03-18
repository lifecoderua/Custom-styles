//ToDo: no closure '</li>' in origin selector. Check is it bug or trick.

function wsTree(treeData, def, customData){
    var cData;
    if(typeof customData == "undefined")
        cData = null;
    else
        cData = customData;

    return {
        tree: treeData, //current tree
        path: def, //array with path to the selected item
        tempPath: def, //path for temporary selection
        selected: null, //selected leaf ID
        prefix: 'wstree_', //li ID prefix, should ends with "_"
        ulPrefix: 'ul_', //ul ID prefix, should ends with "_"
        treeElem: '#origin-tree',
        customData: cData, //custom data provided for user callbacks
        init: false, //init state flag

        /*
        * Tree init
        */
        init: function(conf){
            this.init = true;

            if(conf.apply != undefined) this.apply(conf.apply);
            if(conf.cancel != undefined) this.cancel(conf.cancel);
            this.draw_level('root');
            var x;
            for(x in this.path){
                $("#" + this.prefix + this.path[x]).click();
                $("#" + this.prefix + this.path[x] + ' > .hitarea').click();
            }
            this.path = this.tempPath;

            this.after_apply("#" + this.prefix + this.path[x], true, this.get_names());

            this.init = false;
        },

        /*
        * Get path for the tree and render one level.
        * Also change element for new branch appending.
        */
        draw_level: function(slicePath){
            var slice = this.tree;
            var append = this.treeElem;
            if (slicePath != 'root'){
                var x;
                for(x in slicePath){
                    slice = slice[slicePath[x]]['items'];
                }
                append = '#' + this.ulPrefix + this.prefix + slicePath[x];
            }

            for (var x in slice){
                var item = slice[x];
                //check, if we have items - render a branch, else render a leaf
                if(item.items != undefined){
                    var folder = $('<ul />').attr('id', this.ulPrefix + this.prefix + x);
                    var span = $('<span />').addClass('folder').addClass(item['class']+'_title').html(item['text']);
                    var wrapper = $('<li />').addClass('closed')
                                             .addClass(item['class'])
                                             .attr('id', this.prefix + x);

                    span.appendTo(wrapper);
                    folder.appendTo(wrapper);

                    wrapper.bind('click.preload', {object: this}, this.branch_open).appendTo(append);
                    $(this.treeElem).treeview({
                        add: wrapper
                    });
                } else {
                    var span = $('<span />').addClass('file').addClass(item['class']+'_title').html(item['text']);
                    var wrapper = $('<li />').addClass(item['class']).attr('id', this.prefix + x);

                    span.appendTo(wrapper);

                    wrapper.bind('click.preload', {object: this}, this.item_selected).appendTo(append);
                    $(this.treeElem).treeview({
                        add: wrapper
                    });
                }
            }
        },

        get_path: function(elem){
            //troubles with correct parents traversing. Try to use additional class on li/ul
            var path = new Array();
            path.push($(elem).attr("id").replace(/.*_/g,""));
            $(elem).parents("ul").each(function(){
                path.push($(this).attr("id").replace(/.*_/g,""));
            });
            return path.reverse();
        },

        toggle_selection: function(path){
            $(this.treeElem + " .selected").toggleClass('selected');
            for(var x in path){
                $('#' + this.prefix+path[x]).toggleClass('selected');
            }
        },

        get_names: function(){
            //var names = new Array();
            var names = {
                all: new Array(),
                top: '',
                middle: new Array(),
                leaf: ''
            };
            for(var x in this.path){
                //html? + test "+ span", maybe >span:first be better
                names.all.push($('#' + this.prefix + this.path[x] + " > span").text());
            }
            //clone array
            var tmp = names.all.slice(0);
            names.top = tmp.shift();
            names.middle = tmp.slice(0);
            names.leaf = tmp.pop();
            return names;
        },

        //Binding functions
        apply: function(elem){ $(elem).bind('click', {object: this}, this.on_apply) },
        cancel: function(elem){ $(elem).bind('click', {object: this}, this.on_cancel) },

        //These functions are binded with jQuery, so we have ELEMENT in `this` and
        // pass object through `event` (event.data.object)
        branch_open: function(event){
            var object = event.data.object;
            $(this).unbind('click.preload');
            object.draw_level( object.get_path(this) );
        },

        item_selected: function(event){
            var object = event.data.object;
            object.tempPath = object.get_path(this);
            object.after_select(this);
            object.toggle_selection(object.tempPath);
        },

        on_apply: function(event){
            var object = event.data.object;

            var changed = false;
            //Serializing. We can't compare objects with "==" because even objects with same
            // data stored are different. Works only with objects, not arrays (?!)
            if($.param({'test' : object.path}) != $.param({'test' : object.tempPath})){
                object.path = object.tempPath;
                changed = true;
            }

            var names = object.get_names();

            object.after_apply(this, changed, names);

            HidePopupLayer();
        },

        on_cancel: function(event){
            var object = event.data.object;

            if(object.path != object.tempPath){
                object.toggle_selection(object.path);
            }

            object.after_cancel(this);

            HidePopupLayer();
        },

        //User's callback.
        after_select: function(elem){},
        after_apply: function(elem, changed, names){},
        after_cancel:function(elem){}
    }
}