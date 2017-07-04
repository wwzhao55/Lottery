function select(options) {
    var ele = document.getElementsByClassName(options.srcNode.substr(1));
    if(!ele) throw  Error("Element not exist");
    //
    ele.innerHTML = "";
    var inp = document.createElement('input');
    inp.onclick = function () {
        var list = document.getElementById('mylist');
        list.style.display = 'block';
    };
    ele.appendChild(inp);
    var data = options.data;
    if (!data) throw  Error
    var list = document.createElement('ui');
    list.id = "mylist";
    for (var i = 0; i < data.length; i++) {
        var node = document.createElement('li');
        var text = document.createElement('span');
        text.innerText = data[i];
        text.onclick = function() {
            if (options.onChange typeof fun )
        }
        node.appendChild(text);
        list.appendChild(node);
    }
    list.style.display = 'none';
    ele.appendChild(list);
}