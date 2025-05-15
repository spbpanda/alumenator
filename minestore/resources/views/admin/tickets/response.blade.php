@extends('admin.layout')

@section('content')
        @csrf
        <div class="row" style="display: flex; flex-wrap: wrap; margin-right: -15px; margin-left: -15px;">
   <div class="col-2 col-md-9">
      <div class="card">
         <div class="card-header" style="display:flex; flex:wrap;">
            <div class="badge_pending" style="margin-top: auto;margin-bottom: auto; margin-right: 5px;">PENDING</div>
            <h4 class="card-title" style="font-weight: 500;">Banned for no reason!</h4>
            <h4 class="card-title" style="margin-left: auto; font-weight: 400;">
            #4875</h6>
         </div>
         <div class="card-body">
            <div class="row" style="display: flex; flex: wrap;">
               <button class="btn btn-success" style="margin-left: 4%;"><span class="btn-label"><i class="material-icons">reply</i></span>Reply</button>
               <button class="btn btn-warning" style="margin-left: 20px;"><span class="btn-label"><i class="material-icons">edit</i></span>Note</button>
               <button class="btn btn-error" style="margin-left: 20px; background: #f44336"><span class="btn-label"><i class="material-icons">priority_high</i></span>High priority</button>
               <button class="btn btn-success" style="margin-left: 27%;"><span class="btn-label"><i class="material-icons">done</i></span>Mark as resolved</button>
            </div>
            <hr>
            <div class="row" style="margin-bottom: 15px;padding-right: 30px;padding-left: 10px;">
               <div class="col-2 col-md-2">
                  <img src="https://minotar.net/helm/Hypixel/80.png" alt="Hypixel" style="display: block; width: 80px; border-radius: 4px; margin-left: auto; margin-right: auto; margin-bottom:10px;">
               </div>
               <div class="col-2 col-md-10">
                  <div class="ticket_header" style="display:flex; flex: wrap;">
                     <p style="font-size: 18px;"><a style="font-weight: 400;" href="https://pro.minestorecms.com/profile/Hypixel">Hypixel</a> replied</p>
                     <p style="margin-left: auto; font-weight: 400;">May 20, 2021. 11:36 PM</p>
                  </div>
                  <p style="margin-bottom: 15px;">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam varius, eros in sagittis dictum, lorem ligula dictum est, a dictum dolor nulla ut lorem. Nam rhoncus mollis magna. Quisque ante lorem, ornare ac pulvinar ac, posuere eget nunc. Donec elementum eros felis, vitae lobortis ex iaculis vitae. Sed ac erat urna. Vivamus sed suscipit erat. Vivamus eros dolor, tristique a tincidunt et, sollicitudin sed enim. Donec tempus libero arcu, a porttitor enim tempor et.</p>
               </div>
            </div>
            
            <div class="row" style="margin-bottom: 15px;padding-right: 30px;padding-left: 30px;">
                  <div class="alert alert-warning" style="background-color: #e65e04; box-shadow: 0 4px 20px 0px rgb(0 0 0 / 14%), 0 7px 10px -5px rgb(255 107 0 / 40%);">
                    <span>
                      <b> NOTE - </b> You can watch the proofs in our Discord channel.</span>
                  </div>
            </div>

            <div class="row" style="margin-bottom: 15px;padding-right: 30px;padding-left: 10px;">
                   <div class="col-2 col-md-2">
                      <img src="https://minotar.net/helm/root/80.png" alt="root" style="display: block; width: 80px; border-radius: 4px; margin-left: auto; margin-right: auto; margin-bottom:10px;">
                      <div class="badge_error" style="display: block; font-size: 14px; margin-bottom: 10px;">STAFF</div>
                   </div>
                   <div class="col-2 col-md-10">
                            <div class="ticket_header" style="display:flex; flex: wrap;">
                               <p style="font-size: 18px;"><a style="font-weight: 400;" href="https://pro.minestorecms.com/profile/root">root</a> replied</p>
                               <p style="margin-left: auto; font-weight: 400;">May 20, 2021. 11:36 PM</p>
                            </div>
                      <p>Hey, you were banned because of using cheats. We can provide all evedencies by your request, but the best solution for you it is to buy unban.</p>
                   </div>
            </div>
         </div>
      </div>
   </div>
   <div class="col-6 col-md-3">
      <div class="card">
         <div class="card-header">
            <h4 class="card-title" style="font-weight: 500; text-align: center;">User information</h4>
         </div>
         <div class="card-body text-center">
            <img src="https://minotar.net/helm/Hypixel/80.png" alt="Hypixel" style="width: 80px; border-radius: 4px;">
            <h5 class="card-title" style="font-weight: 400;">Hypixel</h5>
            <h6 class="card-title" style="font-size: 13px;">john.maikelele@hypixel.com</h6>
         </div>
      </div>
      
      <h4 class="card-title" style="font-weight: 500; text-align: center;">Recent Payments <span class="material-icons">paid</span></h4>
      
      <div class="card" style="margin-top: 0;">
            <div class="table-responsive">
                <table class="table" style="font-weight: 500;">
                    <thead class="text-primary" style="color: #ff9800;">
                          <tr><th>
                            Amount
                          </th>
                          <th>
                            Date
                          </th>
                          <th>
                            Status
                          </th>
                        </tr></thead>
                        <tbody>
                          <tr>
                            <td>
                              23.76 USD
                            </td>
                            <td>
                              July 7, 2021, 4:29 PM
                            </td>
                            <td>
                              <div class="badge_pending">PENDING</div>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
      </div>
   </div>
</div>
@endsection
