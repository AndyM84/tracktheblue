<?php $this->layout('template',['title'=>'citation_form'])?>

<form  action=""  method="post">
        
            <div class="form-group">
                <label for="citation_id" >Citation #</label>
                <input type ="text" class="form-control" name="citation_id" value="">
            </div>
        
            <div class="form-group">
                <label for="$citation_date" >What Date was the citation issued?</label>
                <input type ="datetime-local" class="form-control" name="citation_date" value="">
            </div>

            <div class="form-group">
                <label for="city" >City</label>
                <input type ="text" class="form-control" name="city" value="">
            </div>

            <div class="form-group">
                <label for="state" >State</label>
                <input type ="text" class="form-control" name="state" value="">
            </div>

            <div class="form-group">
                <label for="precinct" >Precinct</label>
                <input type ="text" class="form-control" name="precinct" value="">
            </div>    

            <div class="form-group">
                <label for="citation_classification" >Citation Classification</label>
                <input type ="text" class="form-control" name="citation_classification" value="">
            </div>    

            <div class="form-group">
                <label for="mandatory_court" >Mandatory Court</label>
                <div>
                    <input type="radio" id= "yes" name ="mandatory_court">
                    <label for="yes">Yes</label>
                    <input type="radio" id= "no" name ="mandatory_court">
                    <label for="yes">No</label>
                </div>
                
        
            </div>    

            <div class="form-group">
                <label for="fine_ammount" >Fine Amount</label>
                <input type ="number" class="form-control" name="fine_ammount" value="">
            </div>    

            <div class="form-group">
                <label for="vido" >Link to Video of Event</label>
                <input type ="hyperlink" class="form-control" name="video" value="">
            </div>    

            <div class="form-group d-flex justify-content-center">
            <!--  <a href="Civi_into.html"><button type="submit" class="btn btn-default">Your Info</button></a>-->
            <button type="submit" class="btn btn-default" name="submit" value="Submit"> Submit</button>
        </div>

        </form>