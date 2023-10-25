<?php ?>
<div class="sidebar">
    <!--
Tip 1: You can change the color of the sidebar using: data-color="blue | green | orange | red"
  -->
    <div class="sidebar-wrapper ps">
        <div class="logo">
            <a href="javascript:void(0)" class="simple-text logo-mini">
                {{VARIABLE:logo}}
            </a>
            <a href="javascript:void(0)" class="simple-text logo-normal">
                {{VARIABLE:sitename}}
            </a>
        </div>
        <ul class="nav">
            {{LOOP//SIDEBAR:sidebar-item}}
        </ul>
        <div class="ps__rail-x" style="left: 0px; bottom: 0px;"><div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div></div><div class="ps__rail-y" style="top: 0px; right: 0px;"><div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 0px;"></div></div></div>
</div>
