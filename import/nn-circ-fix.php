<?php// 2019-0727 Fix circulation numbers.$circs = array(4037 => 5300,4059 => 124041,4072 => 59343,4079 => 10114,4140 => 7293,4146 => 134874,4187 => 1400,4219 => 11040,4240 => 41376,4244 => 11120,4270 => 7166,4282 => 2522,4293 => 8191,4313 => 2100,4349 => 4694,4363 => 5500,4381 => 39941,4390 => 8000,4392 => 5000,4413 => 59206,4415 => 8191,4417 => 9199,4418 => 8191,4431 => 10524,4432 => 6000,4434 => 3031,4441 => 13337,4450 => 6706,4453 => 14232,4463 => 6706,4499 => 4070,4510 => 32571,4511 => 37998,4517 => 11120,4521 => 1386,4530 => 8000,4532 => 20602,4552 => 3500,4579 => 5860,4590 => 4000,4629 => 6586,4632 => 5500,4640 => 86366,4641 => 4694,4644 => 25399,4653 => 6706,4654 => 12738,4666 => 7152,4671 => 5235,4687 => 161963,4689 => 3700,4693 => 125038,4696 => 25193,4699 => 10279,4704 => 32305,4705 => 9835,4707 => 9056,4726 => 2174,4727 => 8507,4736 => 73037,4744 => 4694,4749 => 10736,4798 => 12071,4801 => 10272,4803 => 125038,4805 => 21620,4824 => 1418,4827 => 86824,4829 => 8342,4833 => 21620,4839 => 59206,4857 => 4501,4861 => 37379,4862 => 3450,4877 => 8191,4881 => 5272,4883 => 4694,4889 => 2501,4904 => 9475,4926 => 10500,4954 => 6706,4961 => 9056,4964 => 9835,4965 => 5787,4968 => 9475,4976 => 6000,4980 => 11040,4981 => 5541,4982 => 17874,4983 => 6706,4984 => 6706,4985 => 4004,4986 => 5946,4987 => 9595,4988 => 7166,4989 => 5067,4993 => 12534,4999 => 42934,5008 => 18325,5016 => 3186,5037 => 5264,5038 => 5264,5048 => 25399,5050 => 4694,5052 => 10937,5097 => 23719,5099 => 24162,5101 => 2500,5104 => 25718,5105 => 4238,5108 => 125038,5140 => 6706,5145 => 9287,5149 => 6000,5174 => 4100,5177 => 9805,5178 => 3468,5189 => 4483,);foreach ( $circs as $key => $value ) {    $circ = get_post_meta( $key, 'nn_circ', true );    echo "$key $circ $value\n";}/*$results = netrics_get_county_data();$results = netrics_write_county_data();$results = netrics_get_region_data();print_r( $results );id      val     circ_new   circ_old4037    5300    5300    54564059    124041  124041  162404072    59343   59343   74124079    10114   10114   125004140    7293    7293    92434146    134874  134874  157524187    1400    1400    216204219    11040   11040   14184240    41376   41376   59324244    11120   11120   55234270    7166    7166    113344282    2522    2522    14004293    8191    8191    50134313    2100    2100    52004349    4694    4694    140004363    5500    5500    188694381    39941   39941   79454390    8000    8000    51684392    5000    5000    85074413    59206   59206   135214415    8191    8191    320454417    9199    91994418    8191    8191    136934431    10524   10524   83204432    6000    6000    110444434    3031    3031    20004441    13337   13337   954164450    6706    6706    63444453    14232   14232   204344463    6706    6706    117954499    4070    4070    77314510    32571   32571   462474511    37998   37998   1162564517    11120   11120   39644521    1386    1386    15314530    8000    8000    89074532    20602   20602   55004552    3500    3500    96264579    5860    5860    89074590    4000    4000    43004629    6586    6586    171034632    5500    5500    34714640    86366   86366   77314641    4694    4694    98794644    25399   25399   118154653    6706    6706    71314654    12738   12738   137604666    7152    7152    130544671    5235    5235    184464687    161963  1619634689    3700    3700    298644693    125038  125038  1389044696    25193   25193   113524699    10279   10279   60634704    32305   32305   159644705    9835    9835    83354707    9056    9056    720004726    2174    2174    72854727    8507    8507    120874736    73037   73037   450544744    4694    4694    33034749    10736   10736   60474798    12071   12071   81914801    10272   10272   81164803    125038  125038  178714805    21620   21620   29254824    1418    1418    77004827    86824   86824   198304829    8342    8342    79004833    21620   21620   59334839    59206   59206   61564857    4501    4501    39354861    37379   37379   486984862    3450    3450    136114877    8191    8191    113934881    5272    5272    141644883    4694    4694    271864889    2501    2501    168564904    9475    9475    85974926    10500   10500   118424954    6706    6706    121554961    9056    9056    178714964    9835    9835    51474965    5787    5787    80694968    9475    9475    791234976    6000    6000    189004980    11040   11040   56864981    5541    5541    109124982    17874   17874   96644983    6706    6706    77544984    6706    6706    99374985    4004    4004    53164986    5946    5946    73564987    9595    9595    91454988    7166    7166    158614989    5067    5067    297304993    12534   12534   72004999    42934   42934   387045008    18325   18325   112585016    3186    3186    1389045037    5264    5264    172215038    5264    5264    50275048    25399   25399   120035050    4694    4694    50005052    10937   10937   170445097    23719   23719   80055099    24162   24162   64095101    2500    2500    690035104    25718   25718   89515105    4238    4238    91305108    125038  125038  41205140    6706    6706    19945145    9287    9287    163335149    6000    6000    274335174    4100    4100    69045177    9805    9805    25305178    3468    3468    35385189    4483    4483    4500 */