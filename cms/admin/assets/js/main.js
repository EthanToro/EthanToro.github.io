/* 
 * main.js
 * Main JavaScript file for the admin section of the cms
 *
 */

$(document).ready(function() {
    var main = $('#main'),
        content = $('.content'),
        sidebar = $('.sidebar'),
        nav = $('nav');

    scaleTabs();
    autoYoga();
    function scaleTabs() {
        var tabs = $('.tab').not('.home, .settings, .user, .notes, .dev'),
            homeTab = $('.home'),
            settingsTab = $('.settings'),
            usersTab = $('.user'),
            notesTab = $('.notes'),
            devTab = $('.dev'),
            width = nav.width() - (homeTab.outerWidth() + settingsTab.outerWidth()+devTab.outerWidth() +notesTab.outerWidth()+ usersTab.outerWidth()),
            tabNumber = tabs.length;

        if (!(width%tabNumber)){
            tabs.each(function() {
                var self = $(this),
                    border = self.outerWidth() - self.width();
                self.width(width/tabNumber - border);
            });
        } else {
            tabs.each(function() {
                var self = $(this),
                    border = self.outerWidth() - self.width();
                self.width(Math.floor(width/tabNumber) - border);
            });
            tabs.eq(0).width( tabs.eq(0).width() + width%tabNumber );
        }
    }
    function autoYoga() {
        var mainDimensions = {width: main.innerWidth(), height: main.innerHeight()};
            
        // Warmup stretch to get us started
        sidebar.height(mainDimensions.height);

        // Continue to stretch throughout
        $(window).on('resize', function() {
            sidebar.height(mainDimensions.height);
        });
    }    
	
});
function sectionGroupMove(dir, id){
	switch(dir){
		case 'up':   $.get("section_ajax.php?action=moveSection&dir=up&id="+id, function(data){ location.reload(); }); break;
		case 'down': $.get("section_ajax.php?action=moveSection&dir=down&id="+id, function(data){ location.reload(); }); break;
	}
}


function newSectionGroup_js() {
    var sgName = prompt('What is the name for the new section group?', '');

    if ((sgName != '') && (sgName != null) && sgName) {
        $.get('section_ajax.php?action=makeSection&name='+sgName,function(d){
            $("#ajaxReload_gname").html(d);
        });
    }
}

function deleteFile(id, fid) {
    $.get('section_ajax.php?action=deleteFile&pid='+id);
    $('#ajax_remove').remove();
    return false;
    return 'to whence you came!';
}
