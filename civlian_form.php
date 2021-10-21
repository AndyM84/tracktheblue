<?php $this->layout('template',['title'=>'civilian_form'])?>

form  action="/action_page.php">
        
            <div class="form-group">
                <label for="age" >Age</label>
                <input type ="text" class="form-control" id="age">
            </div>
        
            <div class="form-group">
                <label for="race" >Race</label>
                <input type ="text" class="form-control" id="race">
            </div>

            <div class="form-group">
                <label for="what_happened" >Ethincity</label>
                <input type ="text_area" class="form-control" id="what_happened">
            </div>

            <div class="form-group">
                <label for="State" >Gender</label>
                <input type ="text" class="form-control" id="State">
            </div>

    
        </form>
        <div class="form-group d-flex justify-content-center">
                
            <a href="event_page.html"> <button type="submit" class="btn btn-default">Your Info</button></a>
         </div>