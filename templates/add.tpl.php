[@header]
<div id="wrapper2">
  <div id="newsletter" class="container">

    [@error]
    [@success]

    <div class="title">
      <h2>Add new project</h2>
      <span class="byline">Here you can upload your project. <br>You can use ZIP, RAR ( if PECL module is installed ) or TAR archives.</span> </div>
    <div class="content">
      <form method="post" action="/add" enctype="multipart/form-data" id="add">
        <div class="row half">
          <div class="6u">
            <input type="text" class="text" name="name" placeholder="Name" required />
          </div>
          <div class="6u">
            <input type="file" name="file" id="file" class="text"  placeholder="File ( Zip / Rar / Tar )" required>
          </div>
        </div>
        <div class="row">
          <div class="12u"> <input type="submit" value="Add project" class="button submit"> </div>
        </div>
      </form>
    </div>
  </div>
</div>
[@footer]