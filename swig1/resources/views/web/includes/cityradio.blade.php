  <?php 
 
  
  foreach ($city_list as $place) {
                                            ?>
                                            <li>
                                                <label>
                                                    <input   
                                                        value="{{$place->id}}" type="checkbox" name="places[]">
                                                    <em></em>
                                                </label>
                                                <span>{{$place->name}} ({{$place->property_count}}) 
                                                    <?php // print_r($muncipality_id) ;  var_dump((in_array($place->id, $muncipality_id)));    ?>
                                                </span>
                                            </li>
                                        <?php } ?>