$(document).ready(function(){
    changeBehaviorDropdown();
    
    $("#menu li a").click(function(){
        $(this).parent().find(".submenu").toggle("lineal");
        $(this).toggleClass("menu-focus");
        $(this).find(".arrow-menu").toggleClass("icon-angle-left");
        $(this).find(".arrow-menu").toggleClass("icon-angle-down");
    });
});

/*
 * Active dropdwon when the 'link' has children
 * Hide the dropdown when click in a other element
 * */
function changeBehaviorDropdown(){
    $("[data-dropdown]").children().data("dropdown-child", true);                
    
    $(document).on("click", "*", function(e){
        var isDropdown = $(e.target).data('dropdown');
        var isChildDropdown = $(e.target).data('dropdown-child');
        var isContent = $(e.target).hasClass("f-dropdown");
        var isIntoContent = false;
        
        $(e.target).parents().each(function(){
            if ($(this).hasClass("f-dropdown")){
                isIntoContent = true;
            }
        });
        
        if (!isDropdown && !isChildDropdown  && !isContent && !isIntoContent){
            $("[data-dropdown]").each(function(){
                Foundation.libs.dropdown.hide($(this));
            });
        }
    });
} 
