insert into symfony.user (id, username, roles, password, email, registration_token, valid, reset_password_token, profil_picture)
values  (1, 'Nonoland', '[]', '$2y$13$a9hBwWUPw8aMpofBW.O26.yTuybvS5hlwVE2AsUNeIjPbYx7aD/MG', 'dartois.nolan.pro@gmail.com', '7eb17ed8428bf82ea8654307459360fcc729063899ba7c06ab387c306aace0b2029d1402689bce37a4be41119322dacead918268f99e5cc93a38df31b0f85c17', 1, null, null);

insert into symfony.trick_group (id, title)
values  (37, 'Grabs'),
        (38, 'Test');

insert into trick (id, trick_group_id, title, description, slug, first_image, images, medias, date_add, date_update)
values  (63, 37, 'Ollie', 'The ollie is a great first trick to learn because it is the basis for a lot of snowboard tricks. Once you learn to ollie, you can jump over obstacles, onto jibs, and start to flat ground spin.', 'ollie', '652ffcbbc681b.jpg', null, '<iframe width="894" height="503" src="https://www.youtube.com/embed/AnI7qGQs0Ic" title="How To Ollie On A Snowboard" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>', '2023-10-18 15:41:47', '2023-10-18 15:41:47'),
        (64, 37, 'Butters', 'These tricks are very easy to learn as all they require is balance and proper weight distribution! You can butter on your nose or tail.', 'butters', '652ffd24f308d.avif', null, '<iframe width="894" height="503" src="https://www.youtube.com/embed/UcamamLlbPg" title="Snowboard Buttering" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>', '2023-10-18 15:43:33', '2023-10-18 15:43:33'),
        (65, 37, 'Indy Grabs', 'Now that you can confidently get air off of jumps, you can learn this easy grab trick!', 'indy-grabs', '652ffd60c6ec5.avif', null, null, '2023-10-18 15:44:32', '2023-10-18 15:44:32');

insert into comment (id, message, date_add, trick_id, user_id)
values  (1, 'Great trick !', '2023-10-18 15:44:58', 63, 1),
        (2, 'OMG \\0/', '2023-10-18 15:45:28', 65, 1);