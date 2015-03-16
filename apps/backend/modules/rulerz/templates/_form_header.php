<link rel="stylesheet" href="/js/CodeMirror/lib/codemirror.css">
<link rel="stylesheet" href="/js/CodeMirror/theme/neat.css">

<script src="/js/CodeMirror/lib/codemirror.js"></script>
<script src="/js/CodeMirror/addon/edit/matchbrackets.js"></script>
<script src="/js/CodeMirror/mode/lua/lua.js"></script>
<script language="javascript" type="text/javascript">
    $(document).ready(function(){
        var editor = CodeMirror.fromTextArea(document.getElementById("rulerz_action"), {
            matchBrackets: true,
            theme: "neat"
        });
    });
</script>