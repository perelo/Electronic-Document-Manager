var src = new Array ();
var i = 0;

src[i++] = '../Js/form.js';
src[i++] = '../Js/ajax.js';
src[i++] = '../Js/liste.js';
src[i++] = '../Js/document.js';

for (var j = 0; j < i; ++j)
{
  document.write ('<script language="javascript" type="text/javascript" src="' + src[j] + '"></script>');
}
