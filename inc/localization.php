<?php
/**
 * Shared WPML strings and reliable fallbacks for theme, REST, and AJAX output.
 *
 * @package News
 */

/**
 * Return strings that must be available in WPML String Translation.
 *
 * The English value is the source string. Russian and Ukrainian values are
 * fallbacks used only while a translation is missing from WPML.
 *
 * @return array
 */
function news_get_translation_catalog() {
	static $catalog = null;

	if ( null !== $catalog ) {
		return $catalog;
	}

	$catalog = array(
		'buttons - Load more' => array(
			'source' => 'Load more',
			'ru'     => 'Загрузить ещё',
			'uk'     => 'Завантажити ще',
		),
		'buttons - Learn more' => array(
			'source' => 'Learn more',
			'ru'     => 'Узнать больше',
			'uk'     => 'Дізнатися більше',
		),
		'buttons - Read more' => array(
			'source' => 'Read more',
			'ru'     => 'Читать дальше',
			'uk'     => 'Читати далі',
		),
		'buttons - Shop now' => array(
			'source' => 'Shop now',
			'ru'     => 'В магазин',
			'uk'     => 'До магазину',
		),
		'buttons - Send' => array(
			'source' => 'Send',
			'ru'     => 'Отправить',
			'uk'     => 'Надіслати',
		),
		'buttons - In store' => array(
			'source' => 'In store',
			'ru'     => 'В магазин',
			'uk'     => 'До магазину',
		),
		'buttons - Add to cart' => array(
			'source' => 'Add to cart',
			'ru'     => 'Добавить в корзину',
			'uk'     => 'Додати до кошика',
		),
		'buttons - to main page' => array(
			'source' => 'To main page',
			'ru'     => 'На главную',
			'uk'     => 'На головну',
		),
		'buttons - Learn More' => array(
			'source' => 'Learn More',
			'ru'     => 'Узнать больше',
			'uk'     => 'Дізнатися більше',
		),
		'buttons - Read More' => array(
			'source' => 'Read More',
			'ru'     => 'Читать дальше',
			'uk'     => 'Читати далі',
		),
		'buttons - Read completely' => array(
			'source' => 'Read completely',
			'ru'     => 'Читать полностью',
			'uk'     => 'Читати повністю',
		),
		'buttons - Adding' => array(
			'source' => 'Adding...',
			'ru'     => 'Добавление...',
			'uk'     => 'Додавання...',
		),
		'buttons - Buy in 1 click' => array(
			'source' => 'Buy Now',
			'ru'     => 'Купить сейчас',
			'uk'     => 'Купити зараз',
		),
		'Button - Subscribe now' => array(
			'source' => 'Subscribe now',
			'ru'     => 'Подписаться',
			'uk'     => 'Підписатися',
		),
		'Button - Place an order' => array(
			'source' => 'Place an order',
			'ru'     => 'Оформить заказ',
			'uk'     => 'Оформити замовлення',
		),
		'button - Proceed the form' => array(
			'source' => 'Proceed the form',
			'ru'     => 'Продолжить оформление',
			'uk'     => 'Продовжити оформлення',
		),
		'Footer - text' => array(
			'source' => 'We believe in the power of nature and its ability to improve our lives. Dive into the world of harmony with us.',
			'ru'     => 'Мы верим в силу природы и её способность улучшать нашу жизнь. Погрузитесь в мир гармонии вместе с нами.',
			'uk'     => 'Ми віримо в силу природи та її здатність покращувати наше життя. Пориньте у світ гармонії разом із нами.',
		),
		'Footer - Contact Us' => array(
			'source' => 'Contact Us',
			'ru'     => 'Связаться с нами',
			'uk'     => 'Зв’язатися з нами',
		),
		'Footer - Learn more' => array(
			'source' => 'Learn more',
			'ru'     => 'Узнать больше',
			'uk'     => 'Дізнатися більше',
		),
		'Footer - Join Our Newsletter' => array(
			'source' => 'Join Our Newsletter',
			'ru'     => 'Подпишитесь на рассылку',
			'uk'     => 'Підпишіться на розсилку',
		),
		'Footer - noties under subscribe' => array(
			'source' => 'You may withdraw your consent at any time!',
			'ru'     => 'Вы можете отозвать своё согласие в любое время!',
			'uk'     => 'Ви можете відкликати свою згоду будь-коли!',
		),
		'Header - free shipping' => array(
			'source' => 'FREE SHIPPING ON ORDERS OVER $125',
			'ru'     => 'БЕСПЛАТНАЯ ДОСТАВКА ЗАКАЗОВ ОТ $125',
			'uk'     => 'БЕЗКОШТОВНА ДОСТАВКА ЗАМОВЛЕНЬ ВІД $125',
		),
		'Header - in Europe' => array(
			'source' => 'In Europe?',
			'ru'     => 'В Европе?',
			'uk'     => 'У Європі?',
		),
		'Archive - page is unavailable' => array(
			'source' => 'This page is temporarily unavailable',
			'ru'     => 'Эта страница временно недоступна',
			'uk'     => 'Ця сторінка тимчасово недоступна',
		),
		'Archive - Posts not found' => array(
			'source' => 'Posts not found',
			'ru'     => 'Публикации не найдены',
			'uk'     => 'Публікації не знайдено',
		),
		'Sidebar - category title' => array(
			'source' => 'Blog Category',
			'ru'     => 'Категории блога',
			'uk'     => 'Категорії блогу',
		),
		'Sidebar - tags title' => array(
			'source' => 'Shop by Health Goal',
			'ru'     => 'Выбор по цели',
			'uk'     => 'Вибір за метою',
		),
		'Sidebar - products title' => array(
			'source' => 'Related products',
			'ru'     => 'Похожие товары',
			'uk'     => 'Схожі товари',
		),
		'Sidebar - posts title' => array(
			'source' => 'Popular Post',
			'ru'     => 'Популярные публикации',
			'uk'     => 'Популярні публікації',
		),
		'Sidebar - pages title' => array(
			'source' => 'Related pages',
			'ru'     => 'Похожие страницы',
			'uk'     => 'Схожі сторінки',
		),
		'Mobile menu - Main page' => array(
			'source' => 'Main page',
			'ru'     => 'Главная',
			'uk'     => 'Головна',
		),
		'Mobile menu - Delivery and payment' => array(
			'source' => 'Delivery and payment',
			'ru'     => 'Доставка и оплата',
			'uk'     => 'Доставка й оплата',
		),
		'Mobile menu - Returns and exchanges' => array(
			'source' => 'Returns and exchanges',
			'ru'     => 'Возврат и обмен',
			'uk'     => 'Повернення й обмін',
		),
		'Mobile menu - Call us' => array(
			'source' => 'Call us',
			'ru'     => 'Позвонить нам',
			'uk'     => 'Зателефонувати нам',
		),
		'Mobile menu - Reviews' => array(
			'source' => 'Reviews',
			'ru'     => 'Отзывы',
			'uk'     => 'Відгуки',
		),
		'Mobile menu - catalog' => array(
			'source' => 'Catalog',
			'ru'     => 'Каталог',
			'uk'     => 'Каталог',
		),
		'Modals - Catalog' => array(
			'source' => 'Catalog',
			'ru'     => 'Каталог',
			'uk'     => 'Каталог',
		),
		'Modals - Scale a photo' => array(
			'source' => 'Scale a photo with a pinch and spread gesture',
			'ru'     => 'Масштабируйте фото жестом сведения и разведения пальцев',
			'uk'     => 'Масштабуйте фото жестом зведення та розведення пальців',
		),
		'Modals - order title' => array(
			'source' => 'We will call you back!',
			'ru'     => 'Мы вам перезвоним!',
			'uk'     => 'Ми вам передзвонимо!',
		),
		'Modals - order text' => array(
			'source' => 'Leave your contact details so we can confirm the payment and delivery details of your order. This will help us make the process faster and more convenient for you!',
			'ru'     => 'Оставьте свои контактные данные, чтобы мы могли подтвердить детали оплаты и доставки вашего заказа. Это поможет сделать процесс быстрее и удобнее для вас!',
			'uk'     => 'Залиште свої контактні дані, щоб ми могли підтвердити деталі оплати та доставки вашого замовлення. Це допоможе зробити процес швидшим і зручнішим для вас!',
		),
		'Modals - order information' => array(
			'source' => 'We use your information solely for confirmation purposes. No marketing or promotional messages will be sent!',
			'ru'     => 'Мы используем вашу информацию исключительно для подтверждения заказа. Никаких маркетинговых или рекламных сообщений отправляться не будет!',
			'uk'     => 'Ми використовуємо вашу інформацію виключно для підтвердження замовлення. Жодних маркетингових чи рекламних повідомлень не надсилатимемо!',
		),
		'Modals - success title' => array(
			'source' => 'Thank you!',
			'ru'     => 'Спасибо!',
			'uk'     => 'Дякуємо!',
		),
		'Modals - success text' => array(
			'source' => 'Your message has been sent successfully.',
			'ru'     => 'Ваше сообщение успешно отправлено.',
			'uk'     => 'Ваше повідомлення успішно надіслано.',
		),
		'Modals - error title' => array(
			'source' => 'Something went wrong',
			'ru'     => 'Что-то пошло не так',
			'uk'     => 'Щось пішло не так',
		),
		'Modals - error text' => array(
			'source' => 'Please try again later.',
			'ru'     => 'Пожалуйста, попробуйте позже.',
			'uk'     => 'Будь ласка, спробуйте пізніше.',
		),
		'Footer - email placeholder' => array(
			'source' => 'Enter Your Email',
			'ru'     => 'Введите электронную почту',
			'uk'     => 'Введіть електронну пошту',
		),
		'Footer - Attention' => array(
			'source' => 'Attention!',
			'ru'     => 'Внимание!',
			'uk'     => 'Увага!',
		),
		'Footer - legal notice' => array(
			'source' => 'We do not sell or promote drugs or illegal substances. All products presented on the website do not contain narcotic substances, do not cause addiction, and are not medicinal products.',
			'ru'     => 'Мы не продаём и не рекламируем наркотики или запрещённые вещества. Все представленные на сайте товары не содержат наркотических веществ, не вызывают зависимости и не являются лекарственными средствами.',
			'uk'     => 'Ми не продаємо й не рекламуємо наркотики або заборонені речовини. Усі представлені на сайті товари не містять наркотичних речовин, не викликають залежності та не є лікарськими засобами.',
		),
		'Social - Share' => array(
			'source' => 'Share',
			'ru'     => 'Поделиться',
			'uk'     => 'Поділитися',
		),
		'Social - Share on Facebook' => array(
			'source' => 'Share on Facebook',
			'ru'     => 'Поделиться в Facebook',
			'uk'     => 'Поділитися у Facebook',
		),
		'Social - Share on Telegram' => array(
			'source' => 'Share on Telegram',
			'ru'     => 'Поделиться в Telegram',
			'uk'     => 'Поділитися у Telegram',
		),
		'Social - Share on Instagram' => array(
			'source' => 'Share on Instagram',
			'ru'     => 'Поделиться в Instagram',
			'uk'     => 'Поділитися в Instagram',
		),
		'Input - Email' => array(
			'source' => 'Email',
			'ru'     => 'Электронная почта',
			'uk'     => 'Електронна пошта',
		),
		'Input - Phone' => array(
			'source' => 'Phone',
			'ru'     => 'Телефон',
			'uk'     => 'Телефон',
		),
		'Input - City' => array(
			'source' => 'City',
			'ru'     => 'Город',
			'uk'     => 'Місто',
		),
		'input - Contacts' => array(
			'source' => '@your_nickname',
			'ru'     => '@ваш_ник',
			'uk'     => '@ваш_нік',
		),
		'Input - First name' => array(
			'source' => 'First name',
			'ru'     => 'Имя',
			'uk'     => 'Ім’я',
		),
		'Input - Last name' => array(
			'source' => 'Last name',
			'ru'     => 'Фамилия',
			'uk'     => 'Прізвище',
		),
		'Input - Address' => array(
			'source' => 'Address',
			'ru'     => 'Адрес',
			'uk'     => 'Адреса',
		),
		'Input - Apartment' => array(
			'source' => 'Apartment, suite, etc.',
			'ru'     => 'Квартира, офис и т. п.',
			'uk'     => 'Квартира, офіс тощо',
		),
		'Input - Postcode' => array(
			'source' => 'Postcode / ZIP',
			'ru'     => 'Почтовый индекс',
			'uk'     => 'Поштовий індекс',
		),
		'Input - Country' => array(
			'source' => 'Country / Region',
			'ru'     => 'Страна / регион',
			'uk'     => 'Країна / регіон',
		),
		'input - Search' => array(
			'source' => 'Search',
			'ru'     => 'Поиск',
			'uk'     => 'Пошук',
		),
		'input - Name' => array(
			'source' => 'Name',
			'ru'     => 'Имя',
			'uk'     => 'Ім’я',
		),
		'input - Surname' => array(
			'source' => 'Surname',
			'ru'     => 'Фамилия',
			'uk'     => 'Прізвище',
		),
		'input - Phone' => array(
			'source' => 'Phone',
			'ru'     => 'Телефон',
			'uk'     => 'Телефон',
		),
		'input - City' => array(
			'source' => 'City',
			'ru'     => 'Город',
			'uk'     => 'Місто',
		),
		'input - Promo code' => array(
			'source' => 'Promo code',
			'ru'     => 'Промокод',
			'uk'     => 'Промокод',
		),
		'input - Your comment' => array(
			'source' => 'Your comment',
			'ru'     => 'Ваш комментарий',
			'uk'     => 'Ваш коментар',
		),
		'Cart - Cart' => array(
			'source' => 'Cart',
			'ru'     => 'Корзина',
			'uk'     => 'Кошик',
		),
		'Cart - Quantity' => array(
			'source' => 'Quantity',
			'ru'     => 'Количество',
			'uk'     => 'Кількість',
		),
		'Cart - Remove' => array(
			'source' => 'Remove',
			'ru'     => 'Удалить',
			'uk'     => 'Видалити',
		),
		'Cart - Cart is empty' => array(
			'source' => 'Cart is empty',
			'ru'     => 'Корзина пуста',
			'uk'     => 'Кошик порожній',
		),
		'Cart - Coupons' => array(
			'source' => 'Coupons',
			'ru'     => 'Купоны',
			'uk'     => 'Купони',
		),
		'Cart - Your order' => array(
			'source' => 'Your order',
			'ru'     => 'Ваш заказ',
			'uk'     => 'Ваше замовлення',
		),
		'Cart - Order amount' => array(
			'source' => 'Order amount',
			'ru'     => 'Сумма заказа',
			'uk'     => 'Сума замовлення',
		),
		'Cart - Shipping' => array(
			'source' => 'Shipping',
			'ru'     => 'Доставка',
			'uk'     => 'Доставка',
		),
		'Cart - Discount Coupon' => array(
			'source' => 'Discount Coupon',
			'ru'     => 'Купон на скидку',
			'uk'     => 'Купон на знижку',
		),
		'Cart - Total' => array(
			'source' => 'Total',
			'ru'     => 'Итого',
			'uk'     => 'Разом',
		),
		'Cart - Place an order' => array(
			'source' => 'Place an order',
			'ru'     => 'Оформить заказ',
			'uk'     => 'Оформити замовлення',
		),
		'Cart - Go to cart' => array(
			'source' => 'Go to cart',
			'ru'     => 'Перейти в корзину',
			'uk'     => 'Перейти до кошика',
		),
		'Cart - Brand' => array(
			'source' => 'Brand',
			'ru'     => 'Бренд',
			'uk'     => 'Бренд',
		),
		'Cart - Size' => array(
			'source' => 'Size',
			'ru'     => 'Размер',
			'uk'     => 'Розмір',
		),
		'Cart - Color' => array(
			'source' => 'Color',
			'ru'     => 'Цвет',
			'uk'     => 'Колір',
		),
		'Cart - Items' => array(
			'source' => 'items',
			'ru'     => 'товаров',
			'uk'     => 'товарів',
		),
		'Checkout - Thank you' => array(
			'source' => 'Thank you. Your order has been accepted.',
			'ru'     => 'Спасибо. Ваш заказ принят.',
			'uk'     => 'Дякуємо. Ваше замовлення прийнято.',
		),
		'Checkout - Order number' => array(
			'source' => 'Order number',
			'ru'     => 'Номер заказа',
			'uk'     => 'Номер замовлення',
		),
		'Checkout - Date' => array(
			'source' => 'Date',
			'ru'     => 'Дата',
			'uk'     => 'Дата',
		),
		'Checkout - City' => array(
			'source' => 'City',
			'ru'     => 'Город',
			'uk'     => 'Місто',
		),
		'Checkout - Post Office' => array(
			'source' => 'Post Office',
			'ru'     => 'Почтовое отделение',
			'uk'     => 'Поштове відділення',
		),
		'Checkout - Payment method' => array(
			'source' => 'Payment method',
			'ru'     => 'Способ оплаты',
			'uk'     => 'Спосіб оплати',
		),
		'Checkout - Full name' => array(
			'source' => 'Full name',
			'ru'     => 'Имя и фамилия',
			'uk'     => 'Ім’я та прізвище',
		),
		'Checkout - Phone' => array(
			'source' => 'Phone',
			'ru'     => 'Телефон',
			'uk'     => 'Телефон',
		),
		'Checkout - Ordering information' => array(
			'source' => 'Ordering information',
			'ru'     => 'Информация о заказе',
			'uk'     => 'Інформація про замовлення',
		),
		'Checkout - Price' => array(
			'source' => 'Price',
			'ru'     => 'Цена',
			'uk'     => 'Ціна',
		),
		'Checkout - Contact' => array(
			'source' => 'Contact',
			'ru'     => 'Контактные данные',
			'uk'     => 'Контактні дані',
		),
		'Checkout - Delivery' => array(
			'source' => 'Delivery',
			'ru'     => 'Доставка',
			'uk'     => 'Доставка',
		),
		'Checkout - Payment' => array(
			'source' => 'Payment',
			'ru'     => 'Оплата',
			'uk'     => 'Оплата',
		),
		'Checkout - Ordering' => array(
			'source' => 'Ordering',
			'ru'     => 'Оформление заказа',
			'uk'     => 'Оформлення замовлення',
		),
		'Product - Quantity' => array(
			'source' => 'Quantity',
			'ru'     => 'Количество',
			'uk'     => 'Кількість',
		),
		'Product - Out of stock' => array(
			'source' => 'Out of stock',
			'ru'     => 'Нет в наличии',
			'uk'     => 'Немає в наявності',
		),
		'Product - In stock' => array(
			'source' => 'In stock',
			'ru'     => 'В наличии',
			'uk'     => 'Є в наявності',
		),
		'Product - Related products' => array(
			'source' => 'Related products',
			'ru'     => 'Похожие товары',
			'uk'     => 'Схожі товари',
		),
		'Product - Reviews' => array(
			'source' => 'Reviews',
			'ru'     => 'Отзывы',
			'uk'     => 'Відгуки',
		),
		'Product - What’s Inside' => array(
			'source' => 'What’s Inside',
			'ru'     => 'Что внутри',
			'uk'     => 'Що всередині',
		),
		'Product - How to use' => array(
			'source' => 'How to use?',
			'ru'     => 'Как использовать?',
			'uk'     => 'Як використовувати?',
		),
		'Product - Sertificates' => array(
			'source' => 'Sertificates',
			'ru'     => 'Сертификаты',
			'uk'     => 'Сертифікати',
		),
		'Comments - Write a review' => array(
			'source' => 'Write a review',
			'ru'     => 'Написать отзыв',
			'uk'     => 'Написати відгук',
		),
		'Comments - Write a comment' => array(
			'source' => 'Write a comment',
			'ru'     => 'Написать комментарий',
			'uk'     => 'Написати коментар',
		),
		'Comments - Comments' => array(
			'source' => 'Comments',
			'ru'     => 'Комментарии',
			'uk'     => 'Коментарі',
		),
		'Comments - Notification' => array(
			'source' => 'Your email will not be published. * Marker for mandatory fields',
			'ru'     => 'Ваш адрес электронной почты не будет опубликован. Поля, обязательные для заполнения, отмечены *',
			'uk'     => 'Вашу адресу електронної пошти не буде опубліковано. Обов’язкові поля позначено *',
		),
		'Comments - Name' => array(
			'source' => 'Name',
			'ru'     => 'Имя',
			'uk'     => 'Ім’я',
		),
		'Comments - Evaluation' => array(
			'source' => 'Evaluation',
			'ru'     => 'Оценка',
			'uk'     => 'Оцінка',
		),
		'Comments - Reply' => array(
			'source' => 'Reply',
			'ru'     => 'Ответить',
			'uk'     => 'Відповісти',
		),
		'Comments - Like comment' => array(
			'source' => 'Like comment',
			'ru'     => 'Нравится комментарий',
			'uk'     => 'Подобається коментар',
		),
		'Comments - Dislike comment' => array(
			'source' => 'Dislike comment',
			'ru'     => 'Не нравится комментарий',
			'uk'     => 'Не подобається коментар',
		),
		'Comments - Write a comment placeholder' => array(
			'source' => 'Write a comment...',
			'ru'     => 'Напишите комментарий...',
			'uk'     => 'Напишіть коментар...',
		),
		'Comments - Post comment' => array(
			'source' => 'Post comment',
			'ru'     => 'Опубликовать комментарий',
			'uk'     => 'Опублікувати коментар',
		),
		'Comments - No comments yet' => array(
			'source' => 'No comments yet.',
			'ru'     => 'Комментариев пока нет.',
			'uk'     => 'Коментарів поки немає.',
		),
		'Comments - Rate' => array(
			'source' => 'Rate...',
			'ru'     => 'Оценка...',
			'uk'     => 'Оцінка...',
		),
		'Comments - Excellent' => array(
			'source' => 'Excellent',
			'ru'     => 'Отлично',
			'uk'     => 'Відмінно',
		),
		'Comments - Good' => array(
			'source' => 'Good',
			'ru'     => 'Хорошо',
			'uk'     => 'Добре',
		),
		'Comments - Average' => array(
			'source' => 'Average',
			'ru'     => 'Средне',
			'uk'     => 'Задовільно',
		),
		'Comments - Not bad' => array(
			'source' => 'Not bad',
			'ru'     => 'Неплохо',
			'uk'     => 'Непогано',
		),
		'Comments - Very poor' => array(
			'source' => 'Very poor',
			'ru'     => 'Очень плохо',
			'uk'     => 'Дуже погано',
		),
		'Comments - Rating required' => array(
			'source' => 'Please select a rating!',
			'ru'     => 'Пожалуйста, выберите оценку!',
			'uk'     => 'Будь ласка, виберіть оцінку!',
		),
		'Page 404 - Page not found' => array(
			'source' => 'Page not found',
			'ru'     => 'Страница не найдена',
			'uk'     => 'Сторінку не знайдено',
		),
		'Page 404 - Use the search' => array(
			'source' => 'Use the search',
			'ru'     => 'Воспользуйтесь поиском',
			'uk'     => 'Скористайтеся пошуком',
		),
		'Search - empty' => array(
			'source' => 'Sorry, your query did not find anything',
			'ru'     => 'К сожалению, по вашему запросу ничего не найдено',
			'uk'     => 'На жаль, за вашим запитом нічого не знайдено',
		),
		'Search - You searched' => array(
			'source' => 'You searched',
			'ru'     => 'Вы искали',
			'uk'     => 'Ви шукали',
		),
		'Strings - tags' => array(
			'source' => 'Tags',
			'ru'     => 'Метки',
			'uk'     => 'Мітки',
		),
		'Strings - related Posts' => array(
			'source' => 'Related Posts',
			'ru'     => 'Похожие публикации',
			'uk'     => 'Схожі публікації',
		),
		'Strings - AI tool to summarize' => array(
			'source' => 'For a quicker read, use AI tool to summarize it to your preferred size',
			'ru'     => 'Для быстрого ознакомления воспользуйтесь ИИ, чтобы получить краткое изложение нужного объёма',
			'uk'     => 'Для швидкого ознайомлення скористайтеся ШІ, щоб отримати стислий виклад потрібного обсягу',
		),
		'Sitemap - Intro' => array(
			'source' => 'A quick index of the main pages, categories, and posts published on the site.',
			'ru'     => 'Быстрый указатель основных страниц, категорий и публикаций сайта.',
			'uk'     => 'Швидкий покажчик основних сторінок, категорій і публікацій сайту.',
		),
		'Sitemap - Pages' => array(
			'source' => 'Pages',
			'ru'     => 'Страницы',
			'uk'     => 'Сторінки',
		),
		'Sitemap - Categories' => array(
			'source' => 'Categories',
			'ru'     => 'Категории',
			'uk'     => 'Категорії',
		),
		'Sitemap - Posts' => array(
			'source' => 'Posts',
			'ru'     => 'Публикации',
			'uk'     => 'Публікації',
		),
		'Menu - Home' => array(
			'source' => 'Home',
			'ru'     => 'Главная',
			'uk'     => 'Головна',
		),
		'Menu - Privacy Policy' => array(
			'source' => 'Privacy Policy',
			'ru'     => 'Политика конфиденциальности',
			'uk'     => 'Політика конфіденційності',
		),
		'Contact - Location' => array(
			'source' => 'Ukraine, Odesa',
			'ru'     => 'Украина, Одесса',
			'uk'     => 'Україна, Одеса',
		),
		'Contact - Hours' => array(
			'source' => 'Mon. - Fri. 9:00 - 18:00',
			'ru'     => 'Пн. – Пт. 9:00 – 18:00',
			'uk'     => 'Пн. – Пт. 9:00 – 18:00',
		),
		'Reviews - schema name' => array(
			'source' => 'Product reviews',
			'ru'     => 'Отзывы о товарах',
			'uk'     => 'Відгуки про товари',
		),
		'AJAX - Required fields' => array(
			'source' => 'Please fill in the required fields and enter a valid email address.',
			'ru'     => 'Пожалуйста, заполните обязательные поля и укажите корректный адрес электронной почты.',
			'uk'     => 'Будь ласка, заповніть обов’язкові поля та вкажіть коректну адресу електронної пошти.',
		),
		'AJAX - Phone required' => array(
			'source' => 'Please enter a valid phone number.',
			'ru'     => 'Пожалуйста, укажите корректный номер телефона.',
			'uk'     => 'Будь ласка, вкажіть коректний номер телефону.',
		),
		'AJAX - Message sent' => array(
			'source' => 'Message sent successfully!',
			'ru'     => 'Сообщение успешно отправлено!',
			'uk'     => 'Повідомлення успішно надіслано!',
		),
		'AJAX - Message failed' => array(
			'source' => 'Could not send the message. Please try again later.',
			'ru'     => 'Не удалось отправить сообщение. Попробуйте позже.',
			'uk'     => 'Не вдалося надіслати повідомлення. Спробуйте пізніше.',
		),
		'AJAX - File too large' => array(
			'source' => 'One or more files exceed 10 MB.',
			'ru'     => 'Один или несколько файлов превышают 10 МБ.',
			'uk'     => 'Один або кілька файлів перевищують 10 МБ.',
		),
		'AJAX - Invalid file type' => array(
			'source' => 'One or more files have an unsupported format.',
			'ru'     => 'Один или несколько файлов имеют недопустимый формат.',
			'uk'     => 'Один або кілька файлів мають непідтримуваний формат.',
		),
		'AJAX - Invalid order data' => array(
			'source' => 'Invalid data for order creation.',
			'ru'     => 'Некорректные данные для создания заказа.',
			'uk'     => 'Некоректні дані для створення замовлення.',
		),
		'AJAX - Order created' => array(
			'source' => 'Order created successfully.',
			'ru'     => 'Заказ успешно создан.',
			'uk'     => 'Замовлення успішно створено.',
		),
		'AJAX - Order creation failed' => array(
			'source' => 'Could not create the order.',
			'ru'     => 'Не удалось создать заказ.',
			'uk'     => 'Не вдалося створити замовлення.',
		),
		'AJAX - Invalid data' => array(
			'source' => 'Invalid data.',
			'ru'     => 'Некорректные данные.',
			'uk'     => 'Некоректні дані.',
		),
		'AJAX - Invalid product' => array(
			'source' => 'Invalid product.',
			'ru'     => 'Некорректный товар.',
			'uk'     => 'Некоректний товар.',
		),
		'AJAX - Add to cart failed' => array(
			'source' => 'Could not add the product to the cart.',
			'ru'     => 'Не удалось добавить товар в корзину.',
			'uk'     => 'Не вдалося додати товар до кошика.',
		),
		'AJAX - Invalid cart item' => array(
			'source' => 'Invalid cart item.',
			'ru'     => 'Некорректная позиция корзины.',
			'uk'     => 'Некоректна позиція кошика.',
		),
		'AJAX - Remove from cart failed' => array(
			'source' => 'Could not remove the product from the cart.',
			'ru'     => 'Не удалось удалить товар из корзины.',
			'uk'     => 'Не вдалося видалити товар із кошика.',
		),
		'AJAX - Already voted' => array(
			'source' => 'You have already voted on this review.',
			'ru'     => 'Вы уже проголосовали за этот отзыв.',
			'uk'     => 'Ви вже проголосували за цей відгук.',
		),
	);

	return $catalog;
}

/**
 * Register all theme strings in WPML String Translation.
 *
 * @return void
 */
function news_register_translation_catalog() {
	$dynamic_option_names = array(
		'Modals - order title',
		'Modals - order text',
		'Modals - order information',
		'Modals - success title',
		'Modals - success text',
		'Modals - error title',
		'Modals - error text',
	);

	foreach ( news_get_translation_catalog() as $name => $translations ) {
		if ( in_array( $name, $dynamic_option_names, true ) ) {
			continue;
		}

		do_action( 'wpml_register_single_string', 'fungi', $name, $translations['source'] );
	}
}
add_action( 'init', 'news_register_translation_catalog', 5 );

/**
 * Resolve the requested site language.
 *
 * @return string
 */
function news_get_current_language() {
	if ( isset( $_REQUEST['lang'] ) ) {
		$request_language = sanitize_key( wp_unslash( $_REQUEST['lang'] ) );

		if ( in_array( $request_language, array( 'en', 'ru', 'uk' ), true ) ) {
			return $request_language;
		}
	}

	if ( has_filter( 'wpml_current_language' ) ) {
		$wpml_language = sanitize_key( (string) apply_filters( 'wpml_current_language', null ) );

		if ( in_array( $wpml_language, array( 'en', 'ru', 'uk' ), true ) ) {
			return $wpml_language;
		}
	}

	if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
		$wpml_language = sanitize_key( (string) ICL_LANGUAGE_CODE );

		if ( in_array( $wpml_language, array( 'en', 'ru', 'uk' ), true ) ) {
			return $wpml_language;
		}
	}

	$locale   = function_exists( 'determine_locale' ) ? determine_locale() : get_locale();
	$language = strtolower( substr( (string) $locale, 0, 2 ) );

	return in_array( $language, array( 'en', 'ru', 'uk' ), true ) ? $language : 'en';
}

/**
 * Translate a registered theme string with a deterministic fallback.
 *
 * @param string $string English source string.
 * @param string $name   WPML string name.
 * @return string
 */
function news_translate_string( $string, $name ) {
	$catalog      = news_get_translation_catalog();
	$translations = isset( $catalog[ $name ] ) ? $catalog[ $name ] : array();
	$source       = isset( $translations['source'] ) ? $translations['source'] : $string;
	$language     = news_get_current_language();

	if ( 'en' === $language ) {
		return $source;
	}

	$wpml_translation = apply_filters( 'wpml_translate_single_string', $source, 'fungi', $name );

	if ( is_string( $wpml_translation ) && $wpml_translation !== $source ) {
		return $wpml_translation;
	}

	return isset( $translations[ $language ] ) ? $translations[ $language ] : $source;
}

/**
 * Translate a dynamic ACF option while keeping its configured English value.
 *
 * @param string $value ACF option value.
 * @param string $name  WPML string name.
 * @return string
 */
function news_translate_option_string( $value, $name ) {
	$value    = (string) $value;
	$language = news_get_current_language();

	if ( '' === $value || 'en' === $language ) {
		return $value;
	}

	do_action( 'wpml_register_single_string', 'fungi', $name, $value );

	$wpml_translation = apply_filters( 'wpml_translate_single_string', $value, 'fungi', $name );

	if ( is_string( $wpml_translation ) && $wpml_translation !== $value ) {
		return $wpml_translation;
	}

	$catalog = news_get_translation_catalog();

	return isset( $catalog[ $name ][ $language ] ) ? $catalog[ $name ][ $language ] : $value;
}

/**
 * Localize ACF option strings used by modal forms.
 *
 * @param array $options Theme options.
 * @return array
 */
function news_translate_theme_options( $options ) {
	if ( ! is_array( $options ) ) {
		return array();
	}

	$fields = array(
		'modal_order' => array(
			'title'  => 'Modals - order title',
			'text'   => 'Modals - order text',
			'inform' => 'Modals - order information',
		),
		'modal_success' => array(
			'title' => 'Modals - success title',
			'text'  => 'Modals - success text',
		),
		'modal_error' => array(
			'title' => 'Modals - error title',
			'text'  => 'Modals - error text',
		),
	);

	foreach ( $fields as $group => $group_fields ) {
		if ( empty( $options[ $group ] ) || ! is_array( $options[ $group ] ) ) {
			continue;
		}

		foreach ( $group_fields as $field => $name ) {
			if ( isset( $options[ $group ][ $field ] ) ) {
				$options[ $group ][ $field ] = news_translate_option_string( $options[ $group ][ $field ], $name );
			}
		}
	}

	return $options;
}

/**
 * Return the exact HTML language code required by the localized page.
 *
 * @return string
 */
function news_get_html_language() {
	return news_get_current_language();
}

/**
 * Normalize known menu labels before translating them.
 *
 * @param string $title Menu item title.
 * @return string
 */
function news_translate_menu_title( $title ) {
	$plain_title = trim( wp_strip_all_tags( (string) $title ) );
	$normalized  = function_exists( 'mb_strtolower' ) ? mb_strtolower( $plain_title, 'UTF-8' ) : strtolower( $plain_title );
	$known_items = array(
		'home'                           => array( 'Home', 'Menu - Home' ),
		'главная'                       => array( 'Home', 'Menu - Home' ),
		'головна'                       => array( 'Home', 'Menu - Home' ),
		'privacy policy'                 => array( 'Privacy Policy', 'Menu - Privacy Policy' ),
		'политика конфиденциальности'    => array( 'Privacy Policy', 'Menu - Privacy Policy' ),
		'політика конфіденційності'      => array( 'Privacy Policy', 'Menu - Privacy Policy' ),
	);

	if ( ! isset( $known_items[ $normalized ] ) ) {
		return $title;
	}

	return news_translate_string( $known_items[ $normalized ][0], $known_items[ $normalized ][1] );
}

/**
 * Translate known WordPress menu items for both Timber and REST consumers.
 *
 * @param array $items Menu items.
 * @return array
 */
function news_translate_menu_items( $items ) {
	if ( ! is_array( $items ) ) {
		return $items;
	}

	if ( is_admin() ) {
		return $items;
	}

	foreach ( $items as $item ) {
		if ( is_object( $item ) && isset( $item->title ) ) {
			$item->title = news_translate_menu_title( $item->title );
		}
	}

	return $items;
}
add_filter( 'wp_get_nav_menu_items', 'news_translate_menu_items', 100 );

/**
 * Translate the home link generated by Yoast breadcrumbs.
 *
 * @param array $links Breadcrumb links.
 * @return array
 */
function news_translate_breadcrumb_home( $links ) {
	if ( ! is_array( $links ) ) {
		return $links;
	}

	if ( isset( $links[0]['text'] ) ) {
		$links[0]['text'] = news_translate_string( 'Home', 'Menu - Home' );
	}

	return $links;
}
add_filter( 'wpseo_breadcrumb_links', 'news_translate_breadcrumb_home', 100 );

/**
 * Return the object ID translated into the current WPML language.
 *
 * The original ID is returned when WPML is unavailable or no translation exists.
 *
 * @param int    $post_id Post or page ID.
 * @param string $type    WordPress post type.
 * @return int
 */
function fh_translated_id( $post_id, $type = 'post' ) {
	$post_id = absint( $post_id );

	if ( ! $post_id || ! function_exists( 'apply_filters' ) ) {
		return $post_id;
	}

	$translated_id = apply_filters( 'wpml_object_id', $post_id, sanitize_key( $type ), true );

	return $translated_id ? absint( $translated_id ) : $post_id;
}

/**
 * Proposed staging-only addition to the News theme.
 * Append the contents AFTER this opening PHP tag to inc/localization.php.
 * Does not change accounts, roles, passwords, email addresses or plugins.
 */

function fh_trust_author_ids() {
	return array( 1, 2, 3 );
}

function fh_trust_author_fields( $user ) {
	if ( ! in_array( (int) $user->ID, fh_trust_author_ids(), true ) || ! current_user_can( 'edit_user', $user->ID ) ) {
		return;
	}
	echo '<h2>Public author profile</h2><table class="form-table">';
	echo '<tr><th><label for="fh_author_slug">Public author URL slug</label></th><td><input name="fh_author_slug" id="fh_author_slug" type="text" class="regular-text" value="' . esc_attr( $user->user_nicename ) . '"><p class="description">Public profile address only. The account login remains unchanged.</p></td></tr>';
	foreach ( array( 'ru' => 'Russian biography', 'uk' => 'Ukrainian biography' ) as $language => $label ) {
		$key = 'fh_author_bio_' . $language;
		echo '<tr><th><label for="' . esc_attr( $key ) . '">' . esc_html( $label ) . '</label></th><td><textarea class="large-text" rows="5" id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '">' . esc_textarea( get_user_meta( $user->ID, $key, true ) ) . '</textarea></td></tr>';
	}
	echo '</table>';
}
add_action( 'show_user_profile', 'fh_trust_author_fields' );
add_action( 'edit_user_profile', 'fh_trust_author_fields' );

function fh_trust_save_author_fields( $user_id ) {
	if ( ! in_array( (int) $user_id, fh_trust_author_ids(), true ) || ! current_user_can( 'edit_user', $user_id ) ) {
		return;
	}
	if ( empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'update-user_' . $user_id ) ) {
		return;
	}
	foreach ( array( 'ru', 'uk' ) as $language ) {
		$key = 'fh_author_bio_' . $language;
		if ( isset( $_POST[ $key ] ) ) {
			update_user_meta( $user_id, $key, sanitize_textarea_field( wp_unslash( $_POST[ $key ] ) ) );
		}
	}
	if ( isset( $_POST['fh_author_slug'] ) ) {
		$slug = sanitize_title( wp_unslash( $_POST['fh_author_slug'] ) );
		$user = get_userdata( $user_id );
		if ( $slug && $user && $slug !== $user->user_nicename ) {
			wp_update_user( array( 'ID' => $user_id, 'user_nicename' => $slug ) );
		}
	}
}
add_action( 'personal_options_update', 'fh_trust_save_author_fields' );
add_action( 'edit_user_profile_update', 'fh_trust_save_author_fields' );

function fh_trust_author_language() {
	$language = apply_filters( 'wpml_current_language', null );
	return in_array( $language, array( 'en', 'ru', 'uk' ), true ) ? $language : 'en';
}

function fh_trust_localized_author_meta( $value, $user_id, $meta_key, $single ) {
	if ( is_admin() || ! in_array( (int) $user_id, fh_trust_author_ids(), true ) || ! in_array( $meta_key, array( 'description', 'user_description' ), true ) ) {
		return $value;
	}
	$language = fh_trust_author_language();
	if ( 'en' === $language ) {
		return $value;
	}
	$bio = get_user_meta( $user_id, 'fh_author_bio_' . $language, true );
	return '' !== $bio ? ( $single ? $bio : array( $bio ) ) : $value;
}
add_filter( 'get_user_metadata', 'fh_trust_localized_author_meta', 20, 4 );

function fh_trust_author_seo_title( $title ) {
	if ( ! is_author() || ! in_array( (int) get_queried_object_id(), fh_trust_author_ids(), true ) ) {
		return $title;
	}
	$language = fh_trust_author_language();
	$labels = array( 'en' => 'Fungi Harmony Author', 'ru' => 'Автор Fungi Harmony', 'uk' => 'Автор Fungi Harmony' );
	return get_the_author_meta( 'display_name', get_queried_object_id() ) . ' | ' . $labels[ $language ];
}
add_filter( 'wpseo_title', 'fh_trust_author_seo_title', 20 );

function fh_trust_author_seo_description( $description ) {
	if ( ! is_author() || ! in_array( (int) get_queried_object_id(), fh_trust_author_ids(), true ) ) {
		return $description;
	}
	$name = get_the_author_meta( 'display_name', get_queried_object_id() );
	$texts = array(
		'en' => 'Publications by %s at Fungi Harmony about mushrooms and mushroom products, with information for readers.',
		'ru' => 'Публикации %s на Fungi Harmony о грибах и грибной продукции: информационные материалы для читателей.',
		'uk' => 'Публікації %s на Fungi Harmony про гриби та грибну продукцію: інформаційні матеріали для читачів.',
	);
	return sprintf( $texts[ fh_trust_author_language() ], $name );
}
add_filter( 'wpseo_metadesc', 'fh_trust_author_seo_description', 20 );

