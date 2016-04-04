<% if $Options %>
    <ul>
        <% loop $Options %>
            <li>
                $Title
                <% if $ChildOptions %>
                    <ul>
                        <% loop $ChildOptions %>
                            <li class="$Class">
                                <input id="$ID" class="checkbox" name="$Name" type="checkbox" value="$Value"<% if isChecked %> checked="checked"<% end_if %><% if isDisabled %> disabled="disabled"<% end_if %> />
                                <label for="$ID">$Title</label>
                            </li>
                        <% end_loop %>
                    </ul>
                <% end_if %>
            </li>
        <% end_loop %>
    </ul>
<% end_if %>
