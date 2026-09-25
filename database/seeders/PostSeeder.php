<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;

/**
 * The 24 articles of the content plan (ke-hoach-noi-dung-seo.md), two per
 * week. The first eight are live; the rest are scheduled one after another
 * so the storefront keeps getting fresh content without anyone touching
 * the admin. Each one follows the same SEO shape the admin form
 * asks for: a lead paragraph that answers the title in about 50 words,
 * "## " sections written as the questions buyers actually type, at least
 * one internal link to a page that sells, and three FAQ rows that feed the
 * FAQPage schema.
 */
class PostSeeder extends Seeder
{
    public function run(): void
    {
        $posts = $this->posts();

        foreach ($posts as $index => $post) {
            Post::updateOrCreate(
                ['slug' => $post['slug']],
                // "+" keeps the keys already on the left, so an article
                // that sets its own status/published_at (the scheduled ones
                // at the end of the list) overrides these defaults.
                $post + [
                    'status' => 'published',
                    // Oldest first, one article every three days, so the
                    // storefront listing is not eight posts on one date.
                    'published_at' => now()->subDays((count($posts) - $index) * 3),
                ]
            );
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function posts(): array
    {
        return [
            [
                'title' => 'Nên mua iPhone nào 2026? Chọn theo nhu cầu và túi tiền',
                'slug' => 'nen-mua-iphone-nao-2026',
                'topic' => 'tu-van',
                'focus_keyword' => 'nên mua iphone nào',
                'excerpt' => 'Bốn nhóm ngân sách, bốn gợi ý máy cụ thể: chọn nhanh chiếc iPhone hợp với cách bạn dùng máy hằng ngày thay vì chọn theo đời máy mới nhất.',
                'body' => <<<'BODY'
Nếu bạn chỉ dùng để nhắn tin, xem video và chụp ảnh gia đình, một chiếc iPhone đời cũ hơn hai đến ba năm vẫn dư sức và rẻ hơn đáng kể. Chỉ khi bạn quay video nhiều, chơi game nặng hoặc cần pin trụ cả ngày dài thì mới nên trả thêm tiền cho bản Pro hoặc Pro Max.
## Chọn theo ngân sách: bốn mức, bốn hướng đi
Đây là cách nhanh nhất để thu hẹp lựa chọn. Bạn xác định số tiền tối đa trước, rồi mới xem máy — làm ngược lại thì gần như luôn đội giá.
- **Dưới 10 triệu**: các đời iPhone cũ hơn, hợp với nhu cầu cơ bản và làm máy phụ.
- **10 đến 18 triệu**: nhóm bán chạy nhất, cân bằng giữa camera, pin và giá.
- **18 đến 25 triệu**: bản Pro của đời trước, thường là lựa chọn khôn ngoan nhất nếu bạn chụp ảnh nhiều.
- **Trên 25 triệu**: bản Pro Max mới nhất, dành cho người quay video và muốn dùng máy 4 đến 5 năm.
Toàn bộ máy đang bán và mức giá hiện tại nằm ở [trang sản phẩm](/san-pham), lọc được theo dòng máy và khoảng giá.
## Bản thường, Pro hay Pro Max khác nhau ở đâu?
Ba điểm khác biệt đáng tiền nhất, xếp theo thứ tự bạn sẽ cảm nhận được: thời lượng pin, hệ thống camera và màn hình. Bản Pro Max luôn có pin lớn nhất trong cùng một đời máy, nên nếu bạn hay đi cả ngày không cắm sạc thì đây là lý do chính đáng nhất để trả thêm tiền.
> Mẹo chọn nhanh: nếu bạn không quay video quá một phút mỗi tuần, tiền chênh lệch giữa bản thường và bản Pro nên để dành cho dung lượng lớn hơn.
## Nên chọn dung lượng bao nhiêu?
128GB đủ cho người dùng phổ thông có sao lưu ảnh lên iCloud. Nếu bạn quay video, tải phim về xem hoặc giữ ảnh trên máy nhiều năm không xoá, hãy chọn 256GB trở lên — dung lượng là thứ duy nhất trên iPhone không nâng cấp được sau khi mua.
## Máy mới hay máy đã qua sử dụng?
Máy cũ rẻ hơn nhưng phải kiểm tra kỹ nguồn gốc và tình trạng pin. Nếu bạn chọn hướng này, xem trước [cách kiểm tra iPhone chính hãng](/tin-tuc/cach-kiem-tra-iphone-chinh-hang) để không trả tiền cho một chiếc máy đã bị thay linh kiện.
## Mua tại phuonghihi thì được gì?
Tất cả máy đều có bảo hành theo [chính sách bảo hành](/chinh-sach/bao-hanh), đổi trả theo [chính sách đổi trả](/chinh-sach/doi-tra), giao hàng toàn quốc. Nếu chưa đủ tiền, bạn có thể xem [chương trình trả góp](/tra-gop) hoặc [thu cũ đổi mới](/thu-cu-doi-moi) để bù một phần.
BODY,
                'faqs' => [
                    ['question' => 'Mua iPhone đời cũ có còn được cập nhật phần mềm không?', 'answer' => 'Có. Apple thường hỗ trợ cập nhật iOS cho máy trong nhiều năm sau ngày ra mắt, nên một chiếc iPhone cũ hơn hai đến ba năm vẫn nhận bản cập nhật bảo mật.'],
                    ['question' => 'Nên mua 128GB hay 256GB?', 'answer' => 'Chọn 128GB nếu bạn sao lưu ảnh lên iCloud và ít quay video. Chọn 256GB trở lên nếu bạn giữ ảnh và video trên máy, vì dung lượng iPhone không nâng cấp được sau khi mua.'],
                    ['question' => 'Có trả góp được không?', 'answer' => 'Có. phuonghihi hỗ trợ trả góp qua thẻ tín dụng và qua công ty tài chính, chi tiết ở trang trả góp.'],
                ],
            ],
            [
                'title' => 'Cách kiểm tra iPhone chính hãng trước khi trả tiền',
                'slug' => 'cach-kiem-tra-iphone-chinh-hang',
                'topic' => 'huong-dan',
                'focus_keyword' => 'cách kiểm tra iphone chính hãng',
                'excerpt' => 'Sáu bước kiểm tra làm được ngay tại quầy trong năm phút: số IMEI, thời hạn bảo hành, tình trạng pin, camera, màn hình và các linh kiện đã bị thay.',
                'body' => <<<'BODY'
Kiểm tra một chiếc iPhone mất khoảng năm phút và chỉ cần chính chiếc máy đó cùng kết nối mạng. Sáu bước dưới đây đi từ thứ dễ làm giả nhất đến thứ khó làm giả nhất, làm đủ cả sáu bước trước khi chuyển tiền.
## Bước 1: So số IMEI ở ba nơi
Bấm gọi **\*#06#** để máy hiện số IMEI, rồi so với số in trên khay SIM và số trong Cài đặt > Cài đặt chung > Giới thiệu. Ba số phải trùng nhau. Lệch một chữ số nghĩa là máy đã bị can thiệp phần cứng.
## Bước 2: Tra bảo hành trên trang của Apple
Nhập số IMEI vào trang kiểm tra bảo hành của Apple. Trang này cho bạn biết máy có tồn tại thật không, đã kích hoạt chưa và còn bao nhiêu ngày bảo hành. Máy mới nguyên seal mà đã kích hoạt từ lâu là dấu hiệu cần hỏi lại người bán.
## Bước 3: Xem tình trạng pin
Vào Cài đặt > Pin > Tình trạng pin và sạc. Con số Dung lượng tối đa cho biết pin còn bao nhiêu phần trăm so với lúc mới. Chi tiết về ngưỡng nên thay pin có ở bài [cách kiểm tra pin iPhone](/tin-tuc/cach-kiem-tra-pin-iphone).
## Bước 4: Tìm dấu vết linh kiện đã thay
Vẫn trong Cài đặt > Giới thiệu, kéo xuống phần Lịch sử linh kiện và dịch vụ. Nếu màn hình, pin hoặc camera từng bị thay bằng linh kiện không chính hãng, máy sẽ báo ở đây.
> Đây là bước nhiều người bỏ qua nhất, và cũng là bước phát hiện ra nhiều vấn đề nhất khi mua máy đã qua sử dụng.
## Bước 5: Thử camera, loa và cảm ứng
Chụp thử cả camera trước và sau, quay một đoạn video ngắn, mở nhạc để nghe loa ngoài, rồi vuốt hết bốn cạnh màn hình. Lỗi cảm ứng viền màn hình là lỗi thường gặp ở máy đã bị thay màn.
## Bước 6: Kiểm tra Face ID và khoá iCloud
Thử đăng ký một khuôn mặt mới để chắc chắn Face ID còn hoạt động. Sau đó vào Cài đặt > Cài đặt chung > Chuyển hoặc Đặt lại iPhone, xác nhận máy không còn bị khoá vào tài khoản iCloud của người khác — máy dính iCloud của người khác thì bạn không dùng được.
## Mua ở đâu thì không phải làm hết sáu bước này?
Khi mua máy tại [phuonghihi](/san-pham), toàn bộ các bước trên đã được kiểm tra trước khi máy lên kệ, và bạn vẫn được kiểm tra lại khi nhận hàng theo [chính sách đổi trả](/chinh-sach/doi-tra).
BODY,
                'faqs' => [
                    ['question' => 'Bấm gì để xem số IMEI trên iPhone?', 'answer' => 'Mở ứng dụng Điện thoại và bấm *#06#, số IMEI sẽ hiện ra ngay. So số này với số in trên khay SIM và số trong Cài đặt > Giới thiệu.'],
                    ['question' => 'Làm sao biết iPhone đã bị thay màn hình hay pin?', 'answer' => 'Vào Cài đặt > Cài đặt chung > Giới thiệu, kéo xuống mục Lịch sử linh kiện và dịch vụ. Linh kiện không chính hãng sẽ được báo tại đây.'],
                    ['question' => 'Máy dính iCloud của người khác có dùng được không?', 'answer' => 'Không. Máy còn bị khoá vào tài khoản iCloud của chủ cũ sẽ không kích hoạt được, và chỉ chủ tài khoản đó mới gỡ được.'],
                ],
            ],
            [
                'title' => 'Cách kiểm tra pin iPhone: chai bao nhiêu phần trăm thì nên thay?',
                'slug' => 'cach-kiem-tra-pin-iphone',
                'topic' => 'huong-dan',
                'focus_keyword' => 'kiểm tra pin iphone',
                'excerpt' => 'Xem dung lượng pin tối đa trong ba bước, hiểu con số đó nói lên điều gì, và biết khi nào thay pin là đáng tiền hơn đổi máy.',
                'body' => <<<'BODY'
Vào Cài đặt > Pin > Tình trạng pin và sạc, đọc con số ở dòng Dung lượng tối đa. Trên 85% là pin còn tốt, từ 80 đến 85% là bắt đầu xuống, dưới 80% thì nên thay pin. Con số này Apple tính sẵn, bạn không cần ứng dụng nào khác.
## Dung lượng tối đa nghĩa là gì?
Nó cho biết pin hiện giữ được bao nhiêu phần trăm điện so với lúc máy còn mới. Pin 100% giữ đúng như ngày xuất xưởng; pin 80% nghĩa là một lần sạc đầy chỉ dùng được khoảng 80% thời gian so với ban đầu.
## Mốc nào thì nên thay pin?
- **Trên 85%**: dùng bình thường, chưa cần làm gì.
- **80 đến 85%**: pin tụt nhanh hơn vào cuối ngày, cân nhắc thay nếu bạn hay đi xa.
- **Dưới 80%**: nên thay. Ở mốc này máy có thể bị giảm hiệu năng để tránh sập nguồn.
- **Máy báo "Cần bảo trì"**: thay càng sớm càng tốt.
## Thay pin hay đổi máy mới?
Quy tắc đơn giản: nếu máy còn dùng mượt và bạn hài lòng với camera, thay pin luôn rẻ hơn đổi máy rất nhiều. Nếu máy đã chậm, chụp ảnh không còn đủ dùng và bạn phải sạc hai lần mỗi ngày thì tiền thay pin nên để dành đổi máy. Xem thử [thu cũ đổi mới](/thu-cu-doi-moi) để biết máy hiện tại bù được bao nhiêu.
## Sạc thế nào cho pin lâu chai?
- Bật Sạc pin tối ưu trong Cài đặt > Pin để máy tự dừng ở 80% qua đêm.
- Tránh để máy cạn sạch rồi mới sạc; cắm sạc khi còn khoảng 20% là vừa.
- Hạn chế để máy nóng lâu, nhiệt độ cao làm pin chai nhanh hơn cả số lần sạc.
> Số lần sạc không quan trọng bằng nhiệt độ. Một chiếc máy hay để trong ô tô giữa trưa sẽ chai pin nhanh hơn máy sạc mỗi ngày hai lần.
## Thay pin tại phuonghihi
Dịch vụ thay pin và các dịch vụ kỹ thuật khác có ở [trang dịch vụ](/dich-vu). Máy còn trong thời gian bảo hành thì xem trước [chính sách bảo hành](/chinh-sach/bao-hanh) vì có trường hợp được xử lý miễn phí.
BODY,
                'faqs' => [
                    ['question' => 'Xem tình trạng pin iPhone ở đâu?', 'answer' => 'Cài đặt > Pin > Tình trạng pin và sạc. Dòng Dung lượng tối đa là con số cần xem.'],
                    ['question' => 'Pin iPhone còn 80% có nên thay không?', 'answer' => 'Nên. Từ mốc 80% trở xuống, máy có thể tự giảm hiệu năng để tránh sập nguồn và thời lượng dùng mỗi ngày giảm rõ.'],
                    ['question' => 'Thay pin có làm mất dữ liệu không?', 'answer' => 'Không. Thay pin không đụng đến bộ nhớ máy, nhưng vẫn nên sao lưu trước khi giao máy cho bất kỳ đơn vị sửa chữa nào.'],
                ],
            ],
            [
                'title' => 'Nên chọn iPhone Pro hay bản thường? So sánh theo cách bạn dùng máy',
                'slug' => 'nen-chon-iphone-pro-hay-ban-thuong',
                'topic' => 'so-sanh',
                'focus_keyword' => 'iphone pro hay bản thường',
                'excerpt' => 'Bốn khác biệt thật sự giữa bản Pro và bản thường, kèm bốn tình huống dùng máy cụ thể để biết khoản chênh lệch có đáng tiền với riêng bạn không.',
                'body' => <<<'BODY'
Bản Pro hơn bản thường ở camera, màn hình, chất liệu khung máy và thời lượng pin. Nếu bạn chủ yếu nhắn tin, lướt mạng xã hội và chụp ảnh đời thường thì bản thường đã đủ; khoản chênh lệch chỉ đáng tiền khi bạn chụp thiếu sáng nhiều hoặc quay video.
## Khác biệt 1: Camera
Đây là lý do chính để mua bản Pro. Bản Pro có thêm ống kính tele để chụp xa, chụp thiếu sáng sạch hơn và có các định dạng quay video chuyên nghiệp hơn. Nếu điện thoại là máy ảnh chính của bạn, tiền bỏ vào đây là đáng.
## Khác biệt 2: Màn hình
Bản Pro có tần số quét cao hơn, nên thao tác vuốt mượt hơn và màn hình sáng hơn khi dùng ngoài trời. Đây là thứ dễ quen và khó quay lại, nhưng cũng là thứ bạn sẽ không nhận ra nếu chưa từng dùng qua.
## Khác biệt 3: Pin
Trong cùng một đời máy, thứ tự thời lượng pin gần như luôn là Pro Max, rồi Pro, rồi bản thường. Nếu bạn ra khỏi nhà từ sáng tới tối không sạc, hãy ưu tiên máy có pin lớn hơn, kể cả khi phải chọn đời máy cũ hơn một năm.
## Khác biệt 4: Khung máy và trọng lượng
Bản Pro dùng chất liệu khung cao cấp hơn nhưng cũng nặng hơn. Người tay nhỏ hoặc hay cầm máy một tay lâu thường thấy bản thường dễ chịu hơn — đây là điểm nên ra cửa hàng cầm thử.
## Bốn tình huống, bốn lựa chọn
- **Chủ yếu nhắn tin và mạng xã hội**: bản thường, ưu tiên dung lượng lớn hơn.
- **Chụp ảnh nhiều, hay chụp buổi tối**: bản Pro.
- **Quay video, làm nội dung**: bản Pro Max, chọn dung lượng từ 256GB.
- **Ngân sách chặt**: bản Pro của đời trước thường đáng tiền hơn bản thường đời mới.
> So sánh trực tiếp hai máy bất kỳ bằng [công cụ so sánh](/so-sanh) — thông số xếp cạnh nhau theo từng dòng, không phải nhớ.
## Xem giá từng bản
Giá hiện tại của tất cả các bản có ở [trang sản phẩm](/san-pham). Nếu khoản chênh lệch là thứ duy nhất cản bạn, xem thêm [trả góp](/tra-gop).
BODY,
                'faqs' => [
                    ['question' => 'iPhone Pro và bản thường khác nhau nhiều nhất ở đâu?', 'answer' => 'Ở camera. Bản Pro có thêm ống kính tele, chụp thiếu sáng tốt hơn và nhiều tuỳ chọn quay video hơn. Sau đó mới đến màn hình, pin và chất liệu khung máy.'],
                    ['question' => 'Nên mua Pro đời cũ hay bản thường đời mới?', 'answer' => 'Nếu bạn chụp ảnh nhiều, bản Pro đời trước thường đáng tiền hơn. Nếu bạn cần pin và phần mềm được hỗ trợ lâu nhất, chọn bản thường đời mới.'],
                    ['question' => 'Bản Pro Max có nặng hơn nhiều không?', 'answer' => 'Có, đây là bản nặng và to nhất trong cùng một đời máy. Nếu hay cầm một tay, nên ra cửa hàng cầm thử trước khi quyết định.'],
                ],
            ],
            [
                'title' => 'Mua iPhone trả góp: cần giấy tờ gì và mỗi tháng trả bao nhiêu?',
                'slug' => 'mua-iphone-tra-gop-can-giay-to-gi',
                'topic' => 'tu-van',
                'focus_keyword' => 'mua iphone trả góp',
                'excerpt' => 'Hai hình thức trả góp phổ biến, giấy tờ cần chuẩn bị cho từng hình thức, cách tự tính số tiền mỗi tháng và những khoản phí cần hỏi trước khi ký.',
                'body' => <<<'BODY'
Có hai đường trả góp: qua thẻ tín dụng và qua công ty tài chính. Trả góp qua thẻ tín dụng chỉ cần chiếc thẻ đang có hạn mức, duyệt ngay tại quầy. Trả góp qua công ty tài chính cần căn cước công dân, thường thêm một giấy tờ chứng minh thu nhập, và chờ duyệt khoảng 15 đến 30 phút.
## Trả góp qua thẻ tín dụng
Ngân hàng chuyển khoản tiền máy thành các kỳ trả đều trên thẻ. Bạn cần thẻ tín dụng còn đủ hạn mức bằng giá máy. Hình thức này thường có kỳ 3, 6, 9 hoặc 12 tháng và là đường nhanh nhất.
## Trả góp qua công ty tài chính
Dành cho người không có thẻ tín dụng. Giấy tờ thường gồm:
- Căn cước công dân còn hạn.
- Bằng lái xe hoặc giấy tờ tuỳ thân thứ hai, tuỳ gói vay.
- Số điện thoại chính chủ đang dùng.
- Trả trước một phần giá trị máy, thường từ 20% đến 40%.
## Tự tính số tiền mỗi tháng
Công thức để tự ước lượng trước khi tới cửa hàng: lấy giá máy trừ đi số tiền trả trước, chia cho số tháng, rồi cộng phần lãi và phí chuyển đổi mà nhân viên báo. Công cụ tính sẵn theo từng máy nằm ở [trang trả góp](/tra-gop).
> Luôn hỏi tổng số tiền phải trả đến hết kỳ, chứ không chỉ hỏi số tiền mỗi tháng. Hai gói cùng "góp 2 triệu mỗi tháng" có thể chênh nhau vài triệu ở tổng cuối.
## Bốn câu nên hỏi trước khi ký
- Tổng số tiền phải trả đến hết kỳ là bao nhiêu?
- Có phí chuyển đổi trả góp hay phí hồ sơ không?
- Trả hết sớm có bị phạt không, phạt bao nhiêu?
- Trả chậm một kỳ thì bị tính phí thế nào?
## Cách giảm số tiền phải vay
Mang máy cũ đi [thu cũ đổi mới](/thu-cu-doi-moi) để trừ thẳng vào giá máy mới, phần còn lại mới đem trả góp. Cách này giảm cả số tiền vay lẫn tiền lãi. Xem giá máy trước ở [trang sản phẩm](/san-pham).
BODY,
                'faqs' => [
                    ['question' => 'Mua iPhone trả góp cần giấy tờ gì?', 'answer' => 'Qua thẻ tín dụng thì chỉ cần thẻ còn đủ hạn mức. Qua công ty tài chính thì cần căn cước công dân, thường thêm một giấy tờ tuỳ thân thứ hai và số điện thoại chính chủ.'],
                    ['question' => 'Trả góp 0% có thật sự không mất thêm đồng nào không?', 'answer' => 'Lãi suất 0% nghĩa là không tính lãi, nhưng vẫn có thể có phí chuyển đổi trả góp hoặc phí hồ sơ. Hãy hỏi tổng số tiền phải trả đến hết kỳ để biết chính xác.'],
                    ['question' => 'Không có thẻ tín dụng thì trả góp được không?', 'answer' => 'Được, qua công ty tài chính. Hình thức này cần trả trước một phần giá máy và chờ duyệt hồ sơ khoảng 15 đến 30 phút.'],
                ],
            ],
            [
                'title' => 'Thu cũ đổi mới iPhone: máy của bạn được định giá thế nào?',
                'slug' => 'thu-cu-doi-moi-iphone-dinh-gia-the-nao',
                'topic' => 'tin-moi',
                'focus_keyword' => 'thu cũ đổi mới iphone',
                'excerpt' => 'Năm yếu tố quyết định giá thu máy cũ, cách chuẩn bị máy để không bị trừ giá oan, và các bước đổi máy tại cửa hàng trong một buổi.',
                'body' => <<<'BODY'
Giá thu một chiếc iPhone cũ phụ thuộc vào năm thứ: đời máy, dung lượng, tình trạng pin, ngoại hình và việc máy đã từng thay linh kiện hay chưa. Chuẩn bị đúng bốn việc trước khi mang máy đi có thể giữ lại được một khoản đáng kể.
## Năm yếu tố quyết định giá
- **Đời máy và dung lượng**: máy đời mới hơn và dung lượng lớn hơn luôn được giá hơn.
- **Tình trạng pin**: pin dưới 80% thường bị trừ tiền thay pin. Cách xem ở bài [kiểm tra pin iPhone](/tin-tuc/cach-kiem-tra-pin-iphone).
- **Ngoại hình**: xước nhẹ trừ ít, móp viền hoặc nứt kính trừ nhiều.
- **Lịch sử sửa chữa**: máy từng thay màn hình hoặc camera không chính hãng bị trừ nhiều nhất.
- **Phụ kiện và hộp**: còn đủ hộp và phụ kiện thường được cộng thêm.
## Bốn việc cần làm trước khi mang máy đi
- Sao lưu dữ liệu lên iCloud hoặc máy tính.
- Đăng xuất iCloud và tắt Tìm iPhone — thiếu bước này cửa hàng không thu được.
- Xoá toàn bộ nội dung và cài đặt.
- Lau máy, gom đủ hộp, cáp và củ sạc nếu còn giữ.
> Đăng xuất iCloud là bước hay bị quên nhất. Máy còn khoá tài khoản thì dù ngoại hình đẹp đến đâu cũng không định giá được.
## Quy trình đổi máy tại cửa hàng
Nhân viên kiểm tra máy khoảng 10 đến 15 phút, báo giá thu, bạn chọn máy mới và phần chênh lệch là số tiền phải bù. Nếu vẫn muốn chia nhỏ khoản bù, ghép tiếp với [trả góp](/tra-gop).
## Xem trước giá máy mới
Danh sách máy đang bán và giá hiện tại có ở [trang sản phẩm](/san-pham). Bảng định giá máy cũ theo từng đời nằm ở [trang thu cũ đổi mới](/thu-cu-doi-moi).
BODY,
                'faqs' => [
                    ['question' => 'Máy bị xước có thu được không?', 'answer' => 'Được. Xước nhẹ chỉ bị trừ một khoản nhỏ; móp viền, nứt kính hoặc lỗi màn hình mới là những thứ bị trừ nhiều.'],
                    ['question' => 'Có bắt buộc đăng xuất iCloud trước khi thu không?', 'answer' => 'Có. Máy còn đăng nhập iCloud và bật Tìm iPhone thì cửa hàng không thể nhận, vì máy vẫn bị khoá vào tài khoản của bạn.'],
                    ['question' => 'Thu cũ đổi mới có kết hợp trả góp được không?', 'answer' => 'Được. Tiền thu máy cũ trừ thẳng vào giá máy mới, phần chênh lệch còn lại có thể trả góp.'],
                ],
            ],
            [
                'title' => 'iPhone cũ có nên mua không? 5 thứ phải kiểm tra trước khi chốt',
                'slug' => 'iphone-cu-co-nen-mua-khong',
                'topic' => 'tu-van',
                'focus_keyword' => 'iphone cũ có nên mua',
                'excerpt' => 'Máy cũ đáng mua khi bạn kiểm tra đủ 5 thứ và người bán chịu bảo hành. Bài này nói rõ kiểm tra gì, trừ giá bao nhiêu là hợp lý, và khi nào nên bỏ qua máy cũ.',
                'body' => <<<'BODY'
Có, nếu bạn kiểm tra đủ năm thứ dưới đây và người bán chịu bảo hành bằng giấy tờ. Cùng số tiền, một chiếc máy cũ thường cho bạn đời máy cao hơn một đến hai bậc so với máy mới. Đổi lại, rủi ro nằm ở pin, linh kiện đã thay và nguồn gốc máy.
## Máy cũ rẻ hơn máy mới bao nhiêu?
Mức chênh phụ thuộc vào đời máy và tình trạng, nhưng quy luật chung là máy càng cũ thì tốc độ mất giá càng chậm lại. Chiếc máy mất giá mạnh nhất trong năm đầu tiên; từ năm thứ ba trở đi giá gần như đi ngang. Vì vậy máy đã qua sử dụng khoảng hai đến ba năm thường là điểm cân bằng tốt nhất giữa giá và thời gian còn dùng được.
## Năm thứ phải kiểm tra
- **Nguồn gốc máy**: số IMEI phải trùng ở cả ba nơi và tra được trên trang của Apple.
- **Tình trạng pin**: xem Dung lượng tối đa trong Cài đặt, dưới 80% thì phải trừ tiền thay pin vào giá.
- **Lịch sử linh kiện**: máy từng thay màn hình hoặc camera không chính hãng phải rẻ hơn rõ rệt.
- **Khoá iCloud và khoá mạng**: máy còn khoá là không dùng được, không có cách nào chữa.
- **Cam kết sau khi mua**: đổi trả trong bao nhiêu ngày, bảo hành bao lâu, ghi ở đâu.
Ba thứ đầu làm được ngay tại quầy trong năm phút, các bước bấm chi tiết nằm ở bài [cách kiểm tra iPhone chính hãng](/tin-tuc/cach-kiem-tra-iphone-chinh-hang).
## Trừ giá bao nhiêu là hợp lý?
Đây là phần người mua hay chịu thiệt vì không biết lấy mốc nào. Cách tính đơn giản: lấy giá người bán đưa ra, trừ đi chi phí phải bỏ thêm để máy về trạng thái dùng tốt.
- Pin dưới 80%: trừ đúng bằng giá thay pin.
- Màn hình đã thay không chính hãng: trừ nhiều, vì đây là thứ ảnh hưởng tới cảm ứng và độ sáng lâu dài.
- Xước nhẹ ở viền hoặc lưng máy: trừ ít, không ảnh hưởng sử dụng.
- Không còn hộp và phụ kiện: trừ một khoản nhỏ.
> Nếu người bán không cho bạn cầm máy kiểm tra đủ năm phút trước khi chuyển tiền, đó đã là câu trả lời. Máy không có gì giấu thì không ai ngại cho kiểm tra.
## Khi nào thì đừng mua máy cũ?
Có ba trường hợp nên bỏ tiền thêm mua máy mới:
- Bạn cần máy dùng liên tục 4 đến 5 năm nữa — máy cũ đã đi hết một phần tuổi thọ pin và phần mềm.
- Bạn mua cho người lớn tuổi hoặc trẻ nhỏ, không ai ở nhà xử lý được khi máy trục trặc.
- Người bán không có cửa hàng cố định và không cam kết đổi trả bằng giấy tờ.
Nếu rơi vào một trong ba trường hợp này, xem hướng chọn máy mới theo ngân sách ở bài [nên mua iPhone nào](/tin-tuc/nen-mua-iphone-nao-2026).
## Mua máy đã qua sử dụng tại phuonghihi
Toàn bộ máy tại cửa hàng đều được kiểm tra đủ năm hạng mục trên trước khi lên kệ, giá niêm yết công khai theo từng tình trạng máy. Bạn vẫn được kiểm tra lại khi nhận hàng theo [chính sách đổi trả](/chinh-sach/doi-tra), và máy có bảo hành theo [chính sách bảo hành](/chinh-sach/bao-hanh). Xem máy đang có tại [trang sản phẩm](/san-pham).
BODY,
                'faqs' => [
                    ['question' => 'iPhone cũ dùng được thêm bao lâu?', 'answer' => 'Tuỳ tình trạng pin và đời máy. Máy đã dùng hai đến ba năm, pin trên 85%, thường còn dùng tốt thêm hai đến ba năm nữa nếu không va đập.'],
                    ['question' => 'Mua iPhone cũ có được bảo hành không?', 'answer' => 'Tuỳ nơi bán. Tại phuonghihi máy đã qua sử dụng vẫn có bảo hành của cửa hàng, điều kiện ghi rõ trong chính sách bảo hành trên website.'],
                    ['question' => 'Pin còn bao nhiêu phần trăm thì mua được?', 'answer' => 'Trên 85% là mua được mà không cần trừ giá. Từ 80 đến 85% nên trả giá thêm. Dưới 80% thì phải trừ đúng chi phí thay pin vào giá máy.'],
                ],
            ],
            [
                'title' => 'iPhone 128GB hay 256GB? Cách tự biết mình cần bao nhiêu',
                'slug' => 'iphone-128gb-hay-256gb',
                'topic' => 'so-sanh',
                'focus_keyword' => 'iphone 128gb hay 256gb',
                'excerpt' => 'Cách tự tính dung lượng mình thật sự cần trong hai phút, dựa trên số ảnh và số phút video bạn quay mỗi tháng, thay vì đoán mò rồi hối hận.',
                'body' => <<<'BODY'
Chọn 128GB nếu bạn bật sao lưu ảnh lên iCloud và hiếm khi quay video. Chọn 256GB nếu bạn giữ ảnh và video trên máy, hoặc quay video 4K. Đây là thứ duy nhất trên iPhone không nâng cấp được sau khi mua, nên thà dư còn hơn thiếu.
## Thực tế bạn dùng được bao nhiêu?
Con số ghi trên hộp không phải con số bạn dùng được. Hệ điều hành và các ứng dụng hệ thống chiếm khoảng 10GB. Nghĩa là máy 128GB còn lại khoảng 118GB, máy 256GB còn khoảng 246GB cho dữ liệu của bạn.
## Mỗi thứ chiếm bao nhiêu dung lượng?
Đây là các mức xấp xỉ, đủ để bạn tự tính:
- Một tấm ảnh chụp thường: khoảng 2 đến 4MB.
- Một phút video Full HD: khoảng 60 đến 90MB.
- Một phút video 4K: khoảng 170 đến 400MB tuỳ tốc độ khung hình.
- Một ứng dụng mạng xã hội sau vài tháng dùng: 2 đến 5GB.
- Một bộ phim tải về xem offline: 2 đến 5GB.
## Tự tính trong hai phút
Lấy ba con số của chính bạn trong một tháng, rồi nhân với 24 tháng — khoảng thời gian trung bình trước khi đổi máy:
- Số ảnh mỗi tháng × 3MB
- Số phút video mỗi tháng × 100MB (Full HD) hoặc × 250MB (4K)
- Cộng thêm 30GB cho ứng dụng, tin nhắn và hệ điều hành
Ra dưới 100GB thì 128GB là đủ. Ra trên 100GB thì lấy 256GB.
> Ví dụ: 200 ảnh và 10 phút video Full HD mỗi tháng → khoảng 1,6GB/tháng → sau 2 năm khoảng 38GB, cộng 30GB nữa là 68GB. Người này chọn 128GB là hợp lý.
## iCloud có thay thế được dung lượng máy không?
Chỉ thay thế được một phần. Khi bật Tối ưu hoá dung lượng, máy giữ bản ảnh nhẹ và đẩy bản gốc lên iCloud, tiết kiệm được đáng kể. Nhưng iCloud là dịch vụ trả tiền hằng tháng, và khi không có mạng bạn không mở được ảnh gốc. Nếu bạn ngại trả phí hằng tháng thì mua thẳng dung lượng lớn hơn một lần vẫn rẻ hơn về lâu dài.
## Chênh lệch giá có đáng không?
Khoản chênh giữa hai bản dung lượng thường nhỏ hơn nhiều so với khoản chênh giữa bản thường và bản Pro. Nếu ngân sách chỉ đủ chọn một trong hai, ưu tiên dung lượng lớn hơn trước, vì camera thì đời nào cũng dùng được còn máy đầy bộ nhớ thì ngày nào cũng khó chịu. Cách cân nhắc giữa các bản máy nằm ở bài [nên chọn iPhone Pro hay bản thường](/tin-tuc/nen-chon-iphone-pro-hay-ban-thuong).
## Xem giá từng bản dung lượng
Giá của từng dung lượng có ở [trang sản phẩm](/san-pham), lọc được theo dung lượng ngay trên bộ lọc bên trái. Chưa chắc chọn đời máy nào thì xem trước bài [nên mua iPhone nào](/tin-tuc/nen-mua-iphone-nao-2026).
BODY,
                'faqs' => [
                    ['question' => 'iPhone 128GB thực tế dùng được bao nhiêu?', 'answer' => 'Khoảng 118GB. Hệ điều hành và ứng dụng hệ thống chiếm khoảng 10GB ngay từ khi máy mới.'],
                    ['question' => 'Có nâng cấp dung lượng iPhone sau khi mua được không?', 'answer' => 'Không. Bộ nhớ iPhone gắn liền bo mạch, không có khe thẻ nhớ. Chọn sai dung lượng thì chỉ còn cách dùng iCloud hoặc đổi máy.'],
                    ['question' => 'Quay video 4K tốn bao nhiêu dung lượng?', 'answer' => 'Khoảng 170 đến 400MB cho mỗi phút, tuỳ tốc độ khung hình. Quay 10 phút mỗi tháng thì sau hai năm đã chiếm khoảng 40 đến 96GB.'],
                ],
            ],
            [
                'title' => 'Cách chuyển dữ liệu từ iPhone cũ sang iPhone mới',
                'slug' => 'cach-chuyen-du-lieu-tu-iphone-cu-sang-iphone-moi',
                'topic' => 'huong-dan',
                // Viết sẵn nhưng chưa hiện với khách: hẹn giờ đăng, tới
                // ngày là tự lên. Đổi ngày hoặc chuyển về bản nháp trong
                // Admin → Tin tức bất cứ lúc nào.
                'status' => 'published',
                'published_at' => now()->addDays(3),
                'focus_keyword' => 'chuyển dữ liệu iphone',
                'excerpt' => 'Ba cách chuyển dữ liệu, cách nào nhanh nhất, những thứ không tự chuyển được, và việc phải làm với máy cũ trước khi giao cho người khác.',
                'body' => <<<'BODY'
Cách nhanh nhất là đặt hai máy cạnh nhau và dùng Bắt đầu nhanh — máy mới sẽ hỏi có muốn chuyển từ máy cũ không ngay trong lúc cài đặt lần đầu. Toàn bộ ảnh, tin nhắn, ứng dụng và cài đặt đi theo. Việc này mất từ 30 phút đến vài tiếng tùy dung lượng.
## Chuẩn bị trước khi bắt đầu
Làm đủ bốn việc này thì quá trình chuyển gần như không bao giờ hỏng giữa chừng:
- Sạc cả hai máy trên 50%, hoặc cắm sạc suốt quá trình.
- Cập nhật máy cũ lên phiên bản iOS mới nhất.
- Kết nối cùng một mạng Wi-Fi.
- Chuẩn bị sẵn mật khẩu Apple Account, vì máy mới sẽ hỏi.
> Đừng bắt đầu khi bạn chuẩn bị ra khỏi nhà. Hai máy phải nằm cạnh nhau cho tới khi xong, rút giữa chừng là phải làm lại từ đầu.
## Cách 1: Bắt đầu nhanh, chuyển thẳng máy sang máy
Đây là cách nên dùng trong hầu hết trường hợp. Bật máy mới, đặt cạnh máy cũ, máy cũ sẽ hiện thông báo hỏi có muốn thiết lập iPhone mới không. Làm theo hướng dẫn trên màn hình, chọn **Chuyển trực tiếp từ iPhone**. Dữ liệu đi thẳng từ máy này sang máy kia, không cần iCloud còn trống.
## Cách 2: Khôi phục từ bản sao lưu iCloud
Dùng khi bạn không còn giữ máy cũ trong tay, hoặc máy cũ đã hỏng. Điều kiện là trước đó máy cũ đã sao lưu lên iCloud. Trên máy mới, ở bước Ứng dụng & Dữ liệu chọn **Khôi phục từ bản sao lưu iCloud**. Cách này phụ thuộc tốc độ mạng và dung lượng iCloud bạn đang có.
## Cách 3: Sao lưu qua máy tính
Dùng khi dữ liệu nhiều mà mạng chậm, hoặc bạn không muốn trả phí iCloud. Cắm máy cũ vào máy tính, sao lưu toàn bộ, rồi cắm máy mới vào và khôi phục từ bản sao lưu đó. Nhớ chọn **mã hoá bản sao lưu**, vì nếu không thì dữ liệu Sức khỏe và mật khẩu đã lưu sẽ không đi theo.
## Những thứ không tự chuyển được
Đây là phần hay khiến người dùng tưởng mất dữ liệu:
- Các ứng dụng ngân hàng và ví điện tử: phải đăng nhập và xác thực lại từ đầu.
- Ứng dụng nhắn tin có mã hoá riêng: cần khôi phục bằng bản sao lưu của chính ứng dụng đó.
- Apple Watch: phải huỷ ghép nối khỏi máy cũ rồi ghép lại với máy mới.
- Thẻ trong Ví và eSIM: thường phải thêm lại thủ công.
## Sau khi chuyển xong, làm gì với máy cũ?
Mở máy mới, kiểm tra đủ ảnh, tin nhắn và danh bạ trước đã. Chắc chắn rồi mới đăng xuất iCloud, tắt Tìm iPhone và xoá toàn bộ nội dung trên máy cũ — thiếu bước này thì không nơi nào thu máy được. Nếu định bán lại, xem [cách máy cũ được định giá](/tin-tuc/thu-cu-doi-moi-iphone-dinh-gia-the-nao) rồi mang tới [chương trình thu cũ đổi mới](/thu-cu-doi-moi).
## Chưa chọn được máy mới?
Xem [máy đang bán](/san-pham), hoặc đọc [nên mua iPhone nào](/tin-tuc/nen-mua-iphone-nao-2026) để chọn theo ngân sách. Mua máy tại phuonghihi thì nhân viên hỗ trợ chuyển dữ liệu ngay tại cửa hàng, bạn không phải tự làm.
BODY,
                'faqs' => [
                    ['question' => 'Chuyển dữ liệu từ iPhone cũ sang mới mất bao lâu?', 'answer' => 'Từ khoảng 30 phút đến vài tiếng, tuỳ lượng dữ liệu và cách chuyển. Chuyển thẳng máy sang máy bằng cáp là nhanh nhất, khôi phục qua iCloud phụ thuộc tốc độ mạng.'],
                    ['question' => 'Không còn giữ máy cũ thì chuyển dữ liệu được không?', 'answer' => 'Được, nếu máy cũ đã từng sao lưu lên iCloud. Trên máy mới chọn Khôi phục từ bản sao lưu iCloud ở bước Ứng dụng và Dữ liệu.'],
                    ['question' => 'Chuyển xong có mất dữ liệu trên máy cũ không?', 'answer' => 'Không. Quá trình chuyển là sao chép, máy cũ vẫn giữ nguyên dữ liệu cho tới khi bạn chủ động xoá toàn bộ nội dung và cài đặt.'],
                ],
            ],
            [
                'title' => 'Mua iPhone cho học sinh sinh viên: chọn thế nào với ngân sách dưới 12 triệu',
                'slug' => 'mua-iphone-cho-hoc-sinh-sinh-vien',
                'topic' => 'tu-van',
                'status' => 'published',
                'published_at' => now()->addDays(7),
                'focus_keyword' => 'iphone cho sinh viên',
                'excerpt' => 'Bốn tiêu chí thật sự quan trọng với người đi học, cách chia ngân sách dưới 12 triệu cho hợp lý, và những thứ không nên trả tiền thêm ở tầm giá này.',
                'body' => <<<'BODY'
Với người đi học, ba thứ đáng tiền nhất theo đúng thứ tự là pin, dung lượng và độ bền. Camera và màn hình cao cấp xếp sau, vì đó là phần đội giá nhanh nhất mà lại ít ảnh hưởng tới việc học. Dưới 12 triệu bạn nên nhắm tới máy đời cũ hơn hai đến ba năm nhưng dung lượng lớn.
## Bốn tiêu chí theo thứ tự ưu tiên
- **Pin**: một ngày học kéo dài từ sáng tới chiều tối, thường không có chỗ cắm sạc. Ưu tiên máy pin còn trên 85%.
- **Dung lượng**: ảnh chụp bảng, tài liệu, video bài giảng cộng dồn rất nhanh. Tối thiểu 128GB.
- **Độ bền**: máy đi học bị rơi nhiều hơn máy để bàn làm việc. Nên tính thêm tiền ốp và dán màn hình vào ngân sách.
- **Thời gian còn được cập nhật**: máy càng mới đời thì càng được hỗ trợ iOS lâu, đây là thứ quyết định máy dùng được mấy năm nữa.
## Chia ngân sách 12 triệu thế nào cho hợp lý
Đừng tiêu hết 12 triệu vào thân máy. Một cách chia thực tế hơn:
- Khoảng 10 đến 11 triệu cho máy.
- Khoảng 300 đến 500 nghìn cho ốp lưng và dán màn hình.
- Phần còn lại để dành cho việc thay pin sau một đến hai năm.
Xem toàn bộ máy trong tầm giá tại [danh sách máy dưới 12 triệu](/san-pham?max_price=12000000).
> Máy đời cũ hơn nhưng dung lượng 256GB gần như luôn là lựa chọn tốt hơn máy mới hơn một đời mà chỉ có 128GB, nếu bạn hay quay video và chụp tài liệu.
## Những thứ không nên trả tiền thêm ở tầm giá này
- **Bản Pro**: chênh lệch tiền lớn, nhưng thứ bạn nhận lại chủ yếu là camera tele và màn hình quét cao — không phục vụ việc học. Cách cân nhắc nằm ở bài [nên chọn iPhone Pro hay bản thường](/tin-tuc/nen-chon-iphone-pro-hay-ban-thuong).
- **Dung lượng 512GB trở lên**: quá dư cho nhu cầu đi học, số tiền đó để dành mua máy đời mới hơn thì đáng hơn.
- **Màu đặc biệt**: một số màu bị hét giá cao hơn dù cấu hình giống hệt.
## Mua máy mới hay máy đã qua sử dụng?
Ở tầm dưới 12 triệu, máy đã qua sử dụng cho bạn đời máy cao hơn rõ rệt với cùng số tiền. Đổi lại phải kiểm tra kỹ — đọc [iPhone cũ có nên mua không](/tin-tuc/iphone-cu-co-nen-mua-khong) trước khi quyết. Nếu người mua là học sinh còn nhỏ và ở nhà không ai xử lý được khi máy trục trặc, nên chọn máy mới cho yên tâm.
## Chưa đủ tiền một lần thì làm sao?
Hai cách thường dùng: [trả góp](/tra-gop) để chia nhỏ theo tháng, hoặc [thu cũ đổi mới](/thu-cu-doi-moi) nếu trong nhà còn máy cũ không dùng tới. Hai cách này ghép được với nhau: lấy tiền máy cũ trừ vào giá, phần còn lại mới trả góp.
BODY,
                'faqs' => [
                    ['question' => 'Sinh viên nên mua iPhone dung lượng bao nhiêu?', 'answer' => 'Tối thiểu 128GB. Nếu hay quay video bài giảng hoặc chụp nhiều tài liệu thì nên lấy 256GB, vì dung lượng iPhone không nâng cấp được sau khi mua.'],
                    ['question' => 'Dưới 12 triệu nên mua máy mới hay máy cũ?', 'answer' => 'Máy đã qua sử dụng cho đời máy cao hơn với cùng số tiền, nhưng phải kiểm tra pin và nguồn gốc kỹ. Máy mới phù hợp hơn khi người dùng là học sinh nhỏ tuổi.'],
                    ['question' => 'Học sinh sinh viên có mua trả góp được không?', 'answer' => 'Được, nhưng hồ sơ trả góp qua công ty tài chính thường cần người đủ 18 tuổi và có giấy tờ tuỳ thân. Người chưa đủ tuổi cần người thân đứng tên.'],
                ],
            ],
            [
                'title' => 'iPhone Pro Max có đáng tiền hơn Pro không?',
                'slug' => 'iphone-pro-max-co-dang-tien-hon-pro',
                'topic' => 'so-sanh',
                'status' => 'published',
                'published_at' => now()->addDays(10),
                'focus_keyword' => 'iphone pro max có đáng mua',
                'excerpt' => 'Pro Max hơn Pro chủ yếu ở pin và màn hình, còn camera đời mới đã gần như giống nhau. Cách tự biết khoản chênh vài triệu có đáng với bạn không.',
                'body' => <<<'BODY'
Pro Max đáng tiền hơn Pro khi bạn cần pin trụ trọn một ngày dài hoặc muốn màn hình lớn nhất để xem phim, đọc tài liệu. Nếu bạn hay cầm máy một tay, ít khi dùng hết pin trước tối, thì bản Pro cho gần như cùng trải nghiệm camera với giá thấp hơn và máy nhẹ hơn hẳn.
## Pro Max hơn Pro ở những điểm nào?
Trong cùng một đời máy, hai bản dùng chung con chip, chung chất liệu khung và chung phần lớn tính năng phần mềm. Khác biệt thật sự nằm ở ba chỗ:
- **Pin**: Pro Max có thân máy to hơn nên chứa được viên pin lớn hơn. Đây là khác biệt bạn cảm nhận rõ nhất mỗi ngày.
- **Màn hình**: Pro Max lớn hơn khoảng nửa inch. Chữ to hơn, xem video đã hơn, gõ phím rộng hơn.
- **Kích thước và trọng lượng**: đổi lại, Pro Max nặng hơn và khó thao tác một tay, nhất là với người tay nhỏ.
## Camera của hai bản có khác nhau không?
Ở các đời cũ thì có. Ví dụ ở đời 15, chỉ bản Pro Max có ống kính tele zoom 5x, bản Pro dừng ở 3x. Từ đời 16 Pro trở đi, Apple đưa hai bản về chung một hệ thống camera, nên nếu bạn chọn đời mới thì camera không còn là lý do để trả thêm tiền cho Pro Max.
> Khi so sánh máy đời cũ, hãy xem kỹ dòng thông số camera của từng bản. Cùng tên "Pro" nhưng khác đời thì khác cả ống kính.
Bạn có thể đặt hai bản cạnh nhau để so thông số ngay trên [trang so sánh](/so-sanh).
## Chênh lệch giá có tương xứng không?
Khoản chênh giữa Pro Max và Pro ở cùng dung lượng thường là vài triệu đồng. Cách tự trả lời câu hỏi "có đáng không" rất đơn giản: hình dung một ngày bình thường của bạn.
- Bạn rời nhà từ sáng, về tới tối, không có chỗ cắm sạc: Pro Max đáng tiền.
- Bạn ngồi văn phòng, cạnh ổ điện gần như cả ngày: Pro là đủ, số tiền chênh nên dùng để lên dung lượng.
- Bạn đọc nhiều, xem phim nhiều trên điện thoại: Pro Max đáng tiền vì màn hình lớn hơn.
- Bạn chạy bộ, đi xe máy, hay để máy trong túi quần: Pro gọn hơn và ít rơi hơn.
## Nên chọn Pro Max đời cũ hay Pro đời mới?
Đây là câu hỏi hay gặp nhất tại cửa hàng. Nếu pin là ưu tiên số một, một chiếc Pro Max đời trước thường vẫn trụ lâu hơn chiếc Pro đời mới. Nếu bạn muốn máy được cập nhật phần mềm lâu nhất và nhẹ tay hơn, chọn Pro đời mới. Trường hợp bạn còn phân vân giữa dòng Pro và bản thường, xem trước bài [nên chọn iPhone Pro hay bản thường](/tin-tuc/nen-chon-iphone-pro-hay-ban-thuong).
## Cầm thử trước khi quyết định
Thông số không nói được cảm giác cầm máy. Nếu có thể, hãy ghé cửa hàng cầm cả hai bản trong vài phút, thử gõ một tin nhắn bằng một tay và bỏ vào túi quần bạn hay mặc. Nhiều người đến với ý định mua Pro Max và ra về với bản Pro, cũng có người làm ngược lại.
## Xem giá và mua tại phuonghihi
Giá từng bản và từng dung lượng có ở [trang sản phẩm](/san-pham). Nếu khoản chênh vài triệu là thứ khiến bạn ngần ngại, có thể chia nhỏ bằng [trả góp](/tra-gop) để lấy đúng chiếc máy mình cần thay vì chọn bản thấp hơn rồi tiếc.
BODY,
                'faqs' => [
                    ['question' => 'iPhone Pro Max hơn Pro ở điểm nào?', 'answer' => 'Chủ yếu ở pin và màn hình. Pro Max có thân máy lớn hơn nên pin lâu hơn và màn hình rộng hơn khoảng nửa inch, đổi lại máy nặng và khó cầm một tay hơn.'],
                    ['question' => 'Camera iPhone Pro và Pro Max có giống nhau không?', 'answer' => 'Từ đời iPhone 16 Pro trở đi, hai bản dùng chung hệ thống camera. Ở một số đời cũ như iPhone 15, chỉ bản Pro Max có ống kính tele zoom 5x.'],
                    ['question' => 'Nên mua Pro Max đời cũ hay Pro đời mới?', 'answer' => 'Nếu ưu tiên pin, Pro Max đời cũ thường trụ lâu hơn. Nếu muốn máy nhẹ hơn và được cập nhật phần mềm lâu nhất, chọn Pro đời mới.'],
                ],
            ],
            [
                'title' => 'iPhone hết pin nhanh: 8 cách tiết kiệm pin làm được ngay',
                'slug' => 'iphone-hao-pin-cach-tiet-kiem-pin',
                'topic' => 'huong-dan',
                'status' => 'published',
                'published_at' => now()->addDays(14),
                'focus_keyword' => 'iphone hao pin',
                'excerpt' => 'Tám thao tác trong Cài đặt giúp iPhone trụ lâu hơn ngay hôm nay, cách tìm ứng dụng đang ăn pin, và dấu hiệu cho thấy lỗi nằm ở viên pin.',
                'body' => <<<'BODY'
Trước tiên hãy vào Cài đặt > Pin để xem ứng dụng nào đang dùng nhiều pin nhất, vì phần lớn trường hợp hao pin đến từ một hai ứng dụng chạy nền. Sau đó bật Chế độ nguồn điện thấp, giảm độ sáng và tắt làm mới ứng dụng trong nền. Nếu làm đủ tám cách dưới đây mà máy vẫn tụt nhanh, lỗi nằm ở viên pin.
## Cách 1: Tìm ứng dụng đang ăn pin
Vào Cài đặt > Pin, kéo xuống phần biểu đồ để xem danh sách ứng dụng theo mức tiêu thụ. Ứng dụng nào chiếm phần lớn mà bạn ít mở, hãy gỡ đi hoặc tắt quyền chạy nền của nó. Mạng xã hội, bản đồ và ứng dụng xem video thường đứng đầu danh sách này.
## Cách 2: Bật Chế độ nguồn điện thấp
Vào Cài đặt > Pin và bật Chế độ nguồn điện thấp, hoặc thêm nút này vào Trung tâm điều khiển để bật nhanh. Máy sẽ tạm giảm các hoạt động nền như tải thư, làm mới ứng dụng và hiệu ứng hình ảnh. Bạn có thể bật cả ngày mà không ảnh hưởng tới nghe gọi hay nhắn tin.
## Cách 3: Giảm độ sáng màn hình
Màn hình là bộ phận tốn pin nhất. Kéo độ sáng xuống mức vừa đủ đọc và bật Độ sáng tự động trong Cài đặt > Trợ năng > Màn hình & Cỡ chữ để máy tự điều chỉnh theo ánh sáng xung quanh.
## Cách 4: Tắt làm mới ứng dụng trong nền
Vào Cài đặt > Cài đặt chung > Làm mới ứng dụng trong nền. Tắt với những ứng dụng không cần cập nhật khi bạn không mở chúng, ví dụ trò chơi, ứng dụng mua sắm, ứng dụng đọc báo.
## Cách 5: Giới hạn định vị
Vào Cài đặt > Quyền riêng tư & Bảo mật > Dịch vụ định vị. Chuyển phần lớn ứng dụng sang **Khi dùng ứng dụng** thay vì **Luôn luôn**. Chỉ những ứng dụng như bản đồ hay gọi xe mới thật sự cần định vị.
## Cách 6: Dùng chế độ tối
Trên các đời iPhone dùng màn hình OLED, chế độ tối giúp tiết kiệm pin vì điểm ảnh màu đen gần như không tiêu thụ điện. Bật trong Cài đặt > Màn hình & Độ sáng.
## Cách 7: Tắt màn hình luôn bật
Các đời iPhone Pro gần đây có tính năng màn hình luôn bật, hiện giờ và thông báo kể cả khi khoá máy. Nếu pin là ưu tiên, tắt nó trong Cài đặt > Màn hình & Độ sáng.
## Cách 8: Để ý sóng yếu và nhiệt độ
Ở nơi sóng yếu, máy phải tăng công suất để giữ kết nối nên hao pin rất nhanh. Khi ở nhà hoặc văn phòng có Wi-Fi ổn định, hãy dùng Wi-Fi. Tránh để máy nóng lâu khi sạc hoặc chơi game, vì nhiệt độ cao làm pin vừa tụt nhanh vừa chai nhanh.
> Sau khi cập nhật iOS hoặc vừa chuyển dữ liệu sang máy mới, máy thường hao pin hơn trong một hai ngày đầu vì đang sắp xếp lại dữ liệu. Đây là bình thường, đừng vội kết luận pin hỏng.
## Làm đủ rồi mà vẫn hao pin thì sao?
Hãy kiểm tra dung lượng pin tối đa theo hướng dẫn ở bài [cách kiểm tra pin iPhone](/tin-tuc/cach-kiem-tra-pin-iphone). Pin đã xuống dưới 80% thì không cài đặt nào cứu được nữa, thay pin là cách duy nhất để máy trụ lại cả ngày.
## Thay pin tại phuonghihi
Dịch vụ thay pin và kiểm tra máy có ở [trang dịch vụ](/dich-vu), báo giá rõ ràng trước khi làm. Nếu máy còn trong thời hạn, xem [chính sách bảo hành](/chinh-sach/bao-hanh) để biết trường hợp của bạn có được hỗ trợ không.
BODY,
                'faqs' => [
                    ['question' => 'Làm sao biết ứng dụng nào làm iPhone hao pin?', 'answer' => 'Vào Cài đặt > Pin và kéo xuống phần biểu đồ. Danh sách bên dưới xếp các ứng dụng theo mức pin đã dùng trong 24 giờ hoặc 10 ngày gần nhất.'],
                    ['question' => 'Bật Chế độ nguồn điện thấp cả ngày có hại máy không?', 'answer' => 'Không. Chế độ này chỉ tạm giảm các hoạt động nền như tải thư và làm mới ứng dụng, không ảnh hưởng tới pin hay phần cứng của máy.'],
                    ['question' => 'Vì sao iPhone hao pin sau khi cập nhật iOS?', 'answer' => 'Sau khi cập nhật, máy chạy nền để sắp xếp lại dữ liệu và ảnh trong một hai ngày đầu nên tốn pin hơn. Nếu tình trạng kéo dài quá vài ngày thì nên kiểm tra dung lượng pin tối đa.'],
                ],
            ],
            [
                'title' => 'Mua iPhone trả góp qua thẻ tín dụng hay công ty tài chính?',
                'slug' => 'tra-gop-the-tin-dung-hay-cong-ty-tai-chinh',
                'topic' => 'tu-van',
                'status' => 'published',
                'published_at' => now()->addDays(17),
                'focus_keyword' => 'trả góp thẻ tín dụng iphone',
                'excerpt' => 'Hai hình thức trả góp khác nhau ở tốc độ duyệt, số tiền trả trước và các khoản phí. Bảng so sánh ngắn và bốn tình huống cụ thể giúp bạn chọn đúng ngay lần đầu.',
                'body' => <<<'BODY'
Nếu bạn đã có thẻ tín dụng còn đủ hạn mức bằng giá máy, trả góp qua thẻ là đường nhanh và gọn nhất: không cần hồ sơ, không cần trả trước. Nếu chưa có thẻ hoặc hạn mức thẻ thấp hơn giá máy, trả góp qua công ty tài chính là lựa chọn còn lại, đổi lại phải trả trước một phần và ký hợp đồng.
## Hai hình thức khác nhau ở đâu?
- **Thẻ tín dụng**: ngân hàng chuyển khoản thanh toán thành các kỳ trả đều trên sao kê thẻ. Bạn không ký thêm hợp đồng nào với cửa hàng.
- **Công ty tài chính**: bạn ký một hợp đồng vay tiêu dùng để mua máy, trả góp hằng tháng cho công ty tài chính chứ không phải cho ngân hàng.
Cả hai đều được áp dụng tại phuonghihi cho đơn từ 3 triệu đồng trở lên, duyệt trong ngày và không cần chứng minh thu nhập. Điều kiện chi tiết nằm ở [trang trả góp](/tra-gop).
## Khi nào nên chọn thẻ tín dụng?
- Hạn mức còn trống của thẻ lớn hơn hoặc bằng giá máy.
- Bạn muốn xong thủ tục ngay tại quầy, không chờ duyệt hồ sơ.
- Bạn không muốn trả trước đồng nào.
Điểm cần hỏi kỹ là **phí chuyển đổi trả góp**. Nhiều ngân hàng tính một khoản phí theo phần trăm giá trị giao dịch khi chuyển sang trả góp, kể cả khi quảng cáo là lãi suất 0%. Mức phí này do ngân hàng phát hành thẻ quyết định, cửa hàng không thay đổi được.
> Sau khi chuyển đổi, hạn mức thẻ bị giữ lại bằng đúng số tiền còn nợ và chỉ được trả dần theo từng kỳ. Nếu bạn hay dùng thẻ cho chi tiêu khác, hãy tính trước phần hạn mức còn lại.
## Khi nào nên chọn công ty tài chính?
- Bạn không có thẻ tín dụng, hoặc hạn mức thẻ không đủ.
- Bạn có sẵn một khoản để trả trước, thường vài chục phần trăm giá máy.
- Bạn chấp nhận chờ duyệt hồ sơ và ký hợp đồng.
Giấy tờ cần chuẩn bị đã được liệt kê trong bài [mua iPhone trả góp cần giấy tờ gì](/tin-tuc/mua-iphone-tra-gop-can-giay-to-gi). Trước khi ký, hãy hỏi rõ tổng số tiền phải trả đến hết kỳ, phí hồ sơ, phí bảo hiểm khoản vay nếu có, và mức phạt khi trả chậm.
## Bốn tình huống cụ thể
- **Đi làm văn phòng, có thẻ hạn mức cao**: chọn thẻ tín dụng, kỳ hạn 6 đến 12 tháng.
- **Sinh viên chưa có thẻ**: chọn công ty tài chính, cần đủ tuổi và giấy tờ tuỳ thân, hoặc người thân đứng tên.
- **Có thẻ nhưng hạn mức chỉ bằng nửa giá máy**: kết hợp thu cũ đổi mới để giảm số tiền cần góp, rồi dùng thẻ cho phần còn lại.
- **Muốn trả hết sớm**: hỏi trước điều kiện tất toán sớm của cả hai hình thức, vì mỗi nơi tính khác nhau.
## Trả chậm một kỳ thì sao?
Với cả hai hình thức, trả chậm đều bị tính phí và có thể được ghi nhận vào lịch sử tín dụng của bạn. Lịch sử này ảnh hưởng tới các khoản vay sau này, kể cả vay mua nhà hay mua xe. Hãy chọn kỳ hạn sao cho số tiền mỗi tháng thoải mái với thu nhập, đừng chọn kỳ ngắn chỉ để trả xong sớm.
## Giảm số tiền cần góp
Cách hiệu quả nhất là mang máy cũ đi [thu cũ đổi mới](/thu-cu-doi-moi): tiền thu máy trừ thẳng vào giá máy mới, phần còn lại mới đem trả góp. Số tiền vay nhỏ hơn thì tổng phí cũng nhỏ hơn. Chọn máy xong, nhân viên sẽ tính sẵn cả hai phương án để bạn so trên giấy trước khi quyết định.
BODY,
                'faqs' => [
                    ['question' => 'Trả góp iPhone qua thẻ tín dụng có cần trả trước không?', 'answer' => 'Không. Chỉ cần thẻ còn hạn mức lớn hơn hoặc bằng giá máy, ngân hàng sẽ chia khoản thanh toán thành các kỳ trả đều trên sao kê thẻ.'],
                    ['question' => 'Trả góp 0% qua thẻ tín dụng có mất phí không?', 'answer' => 'Có thể có. Nhiều ngân hàng tính phí chuyển đổi trả góp theo phần trăm giá trị giao dịch dù lãi suất là 0%. Mức phí do ngân hàng phát hành thẻ quy định.'],
                    ['question' => 'Không có thẻ tín dụng thì trả góp iPhone thế nào?', 'answer' => 'Trả góp qua công ty tài chính. Bạn cần giấy tờ tuỳ thân, trả trước một phần giá máy và ký hợp đồng vay tiêu dùng, thường được duyệt trong ngày.'],
                ],
            ],
            [
                'title' => 'Cách sao lưu iPhone trước khi bán hoặc đổi máy',
                'slug' => 'cach-sao-luu-iphone',
                'topic' => 'huong-dan',
                'status' => 'published',
                'published_at' => now()->addDays(21),
                'focus_keyword' => 'sao lưu iphone',
                'excerpt' => 'Hai cách sao lưu toàn bộ iPhone, cách kiểm tra bản sao lưu đã thật sự hoàn tất, và những dữ liệu phải sao lưu riêng vì không nằm trong bản sao lưu của Apple.',
                'body' => <<<'BODY'
Cách đơn giản nhất là vào Cài đặt > [tên của bạn] > iCloud > Sao lưu iCloud và bấm Sao lưu bây giờ khi máy đang kết nối Wi-Fi. Nếu dung lượng iCloud không đủ, hãy cắm máy vào máy tính và sao lưu qua Finder trên Mac hoặc ứng dụng Apple Devices trên Windows. Sao lưu xong mới được xoá máy.
## Cách 1: Sao lưu lên iCloud
- Kết nối Wi-Fi ổn định và cắm sạc.
- Vào Cài đặt > [tên của bạn] > iCloud > Sao lưu iCloud.
- Bật Sao lưu iPhone này, rồi bấm **Sao lưu bây giờ**.
- Giữ máy kết nối mạng cho tới khi dòng Lần sao lưu thành công cuối cùng hiện giờ vừa xong.
Tài khoản iCloud miễn phí chỉ có 5GB, thường không đủ cho máy dùng lâu năm. Bạn có thể mua thêm dung lượng theo tháng ngay trong mục iCloud, rồi hạ gói hoặc huỷ sau khi đã chuyển xong sang máy mới. Nếu chỉ cần sao lưu một lần trước khi bán máy, cách qua máy tính ở dưới không tốn đồng nào.
> Khi chuẩn bị đổi sang iPhone mới, Apple có tuỳ chọn cho mượn thêm dung lượng iCloud tạm thời để sao lưu. Vào Cài đặt > Cài đặt chung > Chuyển hoặc Đặt lại iPhone > Chuẩn bị cho iPhone mới và làm theo hướng dẫn.
## Cách 2: Sao lưu qua máy tính
Dùng khi dữ liệu nhiều, mạng chậm hoặc không muốn mua thêm iCloud.
- Trên Mac: cắm máy, mở Finder, chọn iPhone ở cột trái, bấm Sao lưu bây giờ.
- Trên Windows: cài ứng dụng Apple Devices, hoặc iTunes với máy tính đời cũ, rồi làm tương tự.
- Đánh dấu ô **Mã hoá bản sao lưu cục bộ** và ghi lại mật khẩu ở nơi an toàn.
Bản sao lưu có mã hoá mới giữ được mật khẩu đã lưu, dữ liệu Sức khỏe và lịch sử cuộc gọi. Quên mật khẩu mã hoá thì không mở được bản sao lưu đó.
## Kiểm tra bản sao lưu đã hoàn tất chưa
Đây là bước nhiều người bỏ qua rồi mất dữ liệu. Với iCloud, xem lại dòng thời gian sao lưu gần nhất trong mục Sao lưu iCloud. Với máy tính, mở phần quản lý bản sao lưu và xem ngày giờ của bản mới nhất. Nếu ngày giờ không phải hôm nay, bản sao lưu chưa chạy xong.
## Những thứ không nằm trong bản sao lưu
- **Ảnh đã bật Ảnh iCloud**: ảnh nằm sẵn trên iCloud, không nằm trong bản sao lưu. Hãy mở Ảnh trên web hoặc máy khác để chắc chắn ảnh đã lên đủ.
- **Tin nhắn Zalo, Messenger và các ứng dụng tương tự**: dùng tính năng sao lưu riêng trong cài đặt của từng ứng dụng.
- **Ứng dụng ngân hàng**: không cần sao lưu, nhưng phải nhớ tên đăng nhập và mật khẩu để kích hoạt lại trên máy mới.
- **Mã xác thực hai lớp**: nếu dùng ứng dụng tạo mã, hãy chuyển sang máy mới trước khi xoá máy cũ, nếu không sẽ tự khoá mình khỏi tài khoản.
## Sao lưu xong thì làm gì tiếp?
Nếu bạn chuyển sang iPhone mới, làm theo bài [cách chuyển dữ liệu từ iPhone cũ sang iPhone mới](/tin-tuc/cach-chuyen-du-lieu-tu-iphone-cu-sang-iphone-moi). Nếu bạn bán hoặc đổi máy, bước tiếp theo là đăng xuất iCloud và xoá sạch dữ liệu. Muốn biết máy cũ được bao nhiêu tiền, xem bài [thu cũ đổi mới iPhone được định giá thế nào](/tin-tuc/thu-cu-doi-moi-iphone-dinh-gia-the-nao) hoặc ước tính ngay ở [trang thu cũ đổi mới](/thu-cu-doi-moi).
BODY,
                'faqs' => [
                    ['question' => 'Sao lưu iPhone lên iCloud mất bao lâu?', 'answer' => 'Từ vài phút đến vài tiếng, tuỳ lượng dữ liệu và tốc độ Wi-Fi. Lần sao lưu đầu tiên luôn lâu nhất, các lần sau chỉ tải phần thay đổi.'],
                    ['question' => 'iCloud hết dung lượng thì sao lưu iPhone thế nào?', 'answer' => 'Sao lưu qua máy tính bằng Finder trên Mac hoặc ứng dụng Apple Devices trên Windows. Nếu đang chuẩn bị đổi iPhone mới, có thể dùng dung lượng iCloud tạm thời Apple cho mượn trong mục Chuẩn bị cho iPhone mới.'],
                    ['question' => 'Bản sao lưu iPhone có gồm tin nhắn Zalo không?', 'answer' => 'Không đầy đủ. Tin nhắn Zalo và các ứng dụng nhắn tin tương tự cần được sao lưu bằng tính năng riêng trong cài đặt của từng ứng dụng.'],
                ],
            ],
            [
                'title' => 'Bảng giá iPhone mới nhất tại phuonghihi theo từng tầm tiền',
                'slug' => 'bang-gia-iphone',
                'topic' => 'tin-moi',
                'status' => 'published',
                'published_at' => now()->addDays(24),
                'focus_keyword' => 'bảng giá iphone',
                'excerpt' => 'Xem giá iPhone đang bán theo bốn tầm tiền, hiểu vì sao cùng một máy lại có nhiều mức giá, và cách lọc ra đúng chiếc máy vừa túi tiền chỉ với hai lần bấm.',
                'body' => <<<'BODY'
Giá iPhone tại phuonghihi được cập nhật trực tiếp trên [trang sản phẩm](/san-pham), nên con số bạn thấy ở đó luôn là giá bán hiện tại. Bài này chia sẵn danh sách theo bốn tầm tiền và giải thích vì sao cùng một đời máy lại có nhiều mức giá khác nhau, để bạn đọc bảng giá mà không bị rối.
## Xem giá theo từng tầm tiền
Bấm vào tầm tiền phù hợp, danh sách sẽ chỉ hiện những máy có giá khởi điểm nằm trong khoảng đó:
- [Dưới 10 triệu](/san-pham?max_price=10000000): các đời cũ hơn, hợp làm máy chính cho nhu cầu cơ bản hoặc máy phụ.
- [Từ 10 đến 18 triệu](/san-pham?min_price=10000000&max_price=18000000): nhóm bán chạy nhất, cân bằng giữa camera, pin và giá.
- [Từ 18 đến 25 triệu](/san-pham?min_price=18000000&max_price=25000000): phần lớn là bản Pro đời trước và bản thường đời mới.
- [Trên 25 triệu](/san-pham?min_price=25000000): bản Pro và Pro Max đời mới nhất.
Muốn xem từ rẻ đến đắt trên toàn bộ danh sách, dùng [sắp xếp giá tăng dần](/san-pham?sort=price_asc).
## Vì sao một máy có nhiều mức giá?
Giá hiện trên thẻ sản phẩm là **giá khởi điểm**, tức bản rẻ nhất của mẫu máy đó. Khi mở trang chi tiết, giá thay đổi theo lựa chọn của bạn:
- **Dung lượng**: mỗi bậc dung lượng cao hơn cộng thêm một khoản. Đây là khoản chênh lớn nhất.
- **Màu sắc**: phần lớn các màu cùng giá, nhưng một số màu mới hoặc hiếm có thể cao hơn.
- **Giá gạch ngang**: khi có chương trình giảm giá, giá cũ hiện gạch ngang bên cạnh giá mới để bạn thấy rõ mức giảm.
> Khi so giá giữa các cửa hàng, hãy so đúng cùng dung lượng và cùng tình trạng máy. Giá khởi điểm của bản 128GB không so được với giá bản 256GB ở nơi khác.
## Giá đã gồm những gì?
Giá niêm yết là giá bán máy, đã gồm bảo hành 12 tháng theo [chính sách bảo hành](/chinh-sach/bao-hanh). Phí giao hàng được tính riêng và hiện rõ ở bước thanh toán trước khi bạn đặt hàng, không phát sinh thêm khi nhận.
## Giá thay đổi khi nào?
Giá iPhone thường giảm mạnh nhất vào hai thời điểm: khi Apple ra đời máy mới, các đời trước được điều chỉnh giá; và trong các đợt khuyến mãi lớn trong năm. Nếu bạn không gấp, bấm nút yêu thích trên trang sản phẩm để lưu lại máy đang để ý và quay lại xem giá sau.
## Máy mới và máy đã qua sử dụng chênh nhau thế nào?
Cùng một mẫu máy, bản đã qua sử dụng luôn rẻ hơn bản mới, mức chênh tuỳ tình trạng pin và ngoại hình. Khi xem giá máy đã qua sử dụng, hãy đọc kỹ phần mô tả trên trang chi tiết thay vì chỉ nhìn con số, vì hai máy chênh nhau vài trăm nghìn có thể khác nhau khá nhiều về pin.
## Chọn tầm tiền nào cho hợp?
Nếu chưa biết nên đặt ngân sách bao nhiêu, bắt đầu từ nhu cầu dung lượng: đọc bài [iPhone 128GB hay 256GB](/tin-tuc/iphone-128gb-hay-256gb) để biết mình cần bao nhiêu, rồi mới chọn tầm tiền. Làm theo thứ tự này sẽ tránh được trường hợp mua được đời máy mới nhưng chỉ đủ tiền cho bản dung lượng thấp nhất.
## Chưa đủ ngân sách thì sao?
Hai cách thường dùng là [trả góp](/tra-gop) để chia nhỏ theo tháng, áp dụng cho đơn từ 3 triệu, và [thu cũ đổi mới](/thu-cu-doi-moi) để lấy tiền máy cũ trừ thẳng vào giá. Ghép hai cách lại, nhiều khách lên được một tầm tiền cao hơn mà số tiền trả mỗi tháng vẫn nhẹ.
BODY,
                'faqs' => [
                    ['question' => 'Giá iPhone trên website phuonghihi có phải giá mới nhất không?', 'answer' => 'Có. Giá trên trang sản phẩm được cập nhật trực tiếp, nên con số hiện ở đó là giá bán tại thời điểm bạn xem.'],
                    ['question' => 'Vì sao giá trên thẻ sản phẩm khác giá khi chọn máy?', 'answer' => 'Giá trên thẻ là giá khởi điểm của bản rẻ nhất. Khi chọn dung lượng hoặc màu khác trong trang chi tiết, giá được cập nhật theo đúng phiên bản bạn chọn.'],
                    ['question' => 'Giá iPhone đã gồm phí giao hàng chưa?', 'answer' => 'Chưa. Phí giao hàng được tính riêng và hiện rõ ở bước thanh toán trước khi đặt hàng, không phát sinh thêm khoản nào khi nhận máy.'],
                ],
            ],
            [
                'title' => 'Mua iPhone chính hãng và hàng xách tay khác nhau thế nào?',
                'slug' => 'iphone-chinh-hang-va-xach-tay-khac-nhau-the-nao',
                'topic' => 'so-sanh',
                'status' => 'published',
                'published_at' => now()->addDays(28),
                'focus_keyword' => 'iphone chính hãng và xách tay',
                'excerpt' => 'Chính hãng và xách tay khác nhau ở bảo hành, SIM và vài tính năng theo thị trường. Cách nhận biết nằm ngay trong Cài đặt, mất chưa tới một phút.',
                'body' => <<<'BODY'
Máy chính hãng là máy được phân phối chính thức cho thị trường Việt Nam, mã máy kết thúc bằng VN/A và được bảo hành tại hệ thống uỷ quyền trong nước. Máy xách tay là máy mua từ thị trường khác mang về, thường rẻ hơn một chút nhưng bảo hành phụ thuộc hoàn toàn vào người bán. Cách xem nằm ngay trong Cài đặt của máy.
## Cách phân biệt trong một phút
Vào Cài đặt > Cài đặt chung > Giới thiệu, tìm dòng **Số kiểu máy**. Nếu dòng này hiện một mã bắt đầu bằng chữ số, bấm vào nó một lần để máy đổi sang mã bắt đầu bằng chữ cái. Hai ký tự trước dấu gạch chéo cuối cùng cho biết thị trường của máy:
- **VN/A**: máy phân phối chính thức tại Việt Nam.
- **LL/A**: máy dành cho thị trường Mỹ.
- **ZA/A, ZP/A**: máy dành cho Singapore, Hồng Kông.
- **J/A**: máy dành cho thị trường Nhật.
Mã máy chỉ cho biết thị trường, không nói máy mới hay cũ. Để kiểm tra nguồn gốc và linh kiện, làm tiếp các bước trong bài [cách kiểm tra iPhone chính hãng](/tin-tuc/cach-kiem-tra-iphone-chinh-hang).
## Khác nhau ở bảo hành
Đây là khác biệt lớn nhất. Máy VN/A được bảo hành tại các trung tâm uỷ quyền trong nước. Với máy xách tay, bảo hành của Apple thường gắn với quốc gia mua máy, nên khi hỏng bạn chủ yếu dựa vào bảo hành của cửa hàng đã bán. Cửa hàng đóng cửa hoặc không giữ lời, bạn gần như không còn chỗ để đòi.
## Khác nhau ở SIM và mạng
- Từ iPhone 14 trở đi, máy bán tại Mỹ không còn khay SIM vật lý, chỉ dùng eSIM. Bạn phải đổi SIM sang eSIM tại nhà mạng trước khi dùng.
- Một số máy xách tay là **máy khoá mạng**, chỉ dùng được với nhà mạng nước ngoài, phải dùng thêm SIM ghép để chạy ở Việt Nam. Loại máy này có thể mất sóng sau khi cập nhật iOS.
> Máy xách tay rẻ bất thường so với mặt bằng chung thường là máy khoá mạng. Hỏi thẳng người bán và kiểm tra lại trong Cài đặt trước khi trả tiền.
## Khác nhau ở một vài tính năng
Phần lớn tính năng giống hệt nhau. Một vài khác biệt theo thị trường vẫn tồn tại, ví dụ máy dành cho thị trường Nhật luôn phát tiếng chụp ảnh và không tắt được. Nếu bạn quan tâm một tính năng cụ thể, hãy hỏi rõ trước khi mua.
## Máy xách tay có rẻ hơn thật không?
Chênh lệch thường không lớn như nhiều người nghĩ, nhất là khi tính thêm rủi ro bảo hành. Hãy so theo công thức: giá máy xách tay, cộng chi phí bạn có thể phải tự bỏ ra nếu máy hỏng trong năm đầu. Nếu khoản chênh sau khi cộng chỉ còn vài trăm nghìn, máy chính hãng là lựa chọn an toàn hơn.
## Mua máy nào cũng cần giữ gì?
Dù chọn loại nào, hãy giữ hoá đơn có ghi số IMEI và điều kiện bảo hành bằng văn bản. Tại phuonghihi, bảo hành 12 tháng được tính theo số IMEI trên hoá đơn và tra được tại quầy hoặc qua hotline, chi tiết ở [chính sách bảo hành](/chinh-sach/bao-hanh). Trước khi chốt, bạn cứ kiểm tra số kiểu máy ngay trên máy, nhân viên sẽ chỉ bạn cách xem. Nếu đang cân nhắc máy đã qua sử dụng, đọc thêm bài [iPhone cũ có nên mua không](/tin-tuc/iphone-cu-co-nen-mua-khong).
BODY,
                'faqs' => [
                    ['question' => 'Làm sao biết iPhone là hàng chính hãng Việt Nam?', 'answer' => 'Vào Cài đặt > Cài đặt chung > Giới thiệu, bấm vào dòng Số kiểu máy cho tới khi hiện mã bắt đầu bằng chữ cái. Mã kết thúc bằng VN/A là máy phân phối chính thức tại Việt Nam.'],
                    ['question' => 'iPhone xách tay có được Apple bảo hành ở Việt Nam không?', 'answer' => 'Thường là không. Bảo hành của Apple cho iPhone thường gắn với quốc gia mua máy, nên máy xách tay chủ yếu dựa vào bảo hành của cửa hàng đã bán.'],
                    ['question' => 'iPhone Mỹ có dùng được SIM Việt Nam không?', 'answer' => 'Được nếu máy không khoá mạng. Từ iPhone 14 trở đi, máy bán tại Mỹ chỉ dùng eSIM, nên bạn cần đổi SIM sang eSIM tại nhà mạng Việt Nam.'],
                ],
            ],
            [
                'title' => 'Face ID không hoạt động: tự xử lý trước khi mang ra tiệm',
                'slug' => 'face-id-khong-hoat-dong',
                'topic' => 'huong-dan',
                'status' => 'published',
                'published_at' => now()->addDays(31),
                'focus_keyword' => 'face id không hoạt động',
                'excerpt' => 'Bảy bước tự kiểm tra xếp từ dễ đến khó, giải quyết phần lớn lỗi Face ID trong mười phút, và dấu hiệu cho biết lỗi nằm ở phần cứng cần mang đi kiểm tra.',
                'body' => <<<'BODY'
Phần lớn lỗi Face ID đến từ những thứ rất đơn giản: cảm biến phía trên màn hình bị bẩn hoặc bị che, máy cần khởi động lại, hoặc khuôn mặt đã đăng ký không còn khớp. Làm lần lượt bảy bước dưới đây trước. Nếu máy báo Face ID không khả dụng ngay cả sau khi đặt lại, lỗi nằm ở phần cứng.
## Bước 1: Lau sạch cụm cảm biến
Face ID dùng cụm camera và cảm biến ở phần trên cùng của màn hình. Mồ hôi, bụi, dầu từ da mặt đủ làm cảm biến nhận sai. Lau nhẹ bằng khăn mềm, khô, không dùng cồn đậm đặc.
## Bước 2: Kiểm tra ốp lưng và miếng dán màn hình
Miếng dán cường lực loại rẻ hoặc dán lệch có thể che một phần cảm biến. Ốp lưng có viền trước quá cao cũng gây lỗi tương tự. Tháo ốp, nếu nghi miếng dán thì thử bóc ra, rồi thử lại.
## Bước 3: Khởi động lại máy
Tắt nguồn hẳn, chờ khoảng ba mươi giây rồi bật lại. Sau khi bật, máy sẽ yêu cầu nhập mật mã một lần trước khi cho dùng Face ID, đây là bình thường.
## Bước 4: Kiểm tra cài đặt Face ID
Vào Cài đặt > Face ID & Mật mã, xem các công tắc Mở khoá iPhone, App Store, Tự điền mật khẩu đã bật chưa. Nhiều trường hợp "hỏng Face ID" thực ra chỉ là một công tắc bị tắt sau khi cập nhật.
## Bước 5: Để ý tư thế và vật che mặt
- Giữ máy cách mặt khoảng một cánh tay, nhìn thẳng vào màn hình.
- Kính râm chặn tia hồng ngoại có thể làm Face ID không nhận.
- Từ iPhone 12 trở đi, bạn có thể bật tuỳ chọn dùng Face ID khi đeo khẩu trang trong cùng mục cài đặt.
- Mở khoá khi cầm ngang chỉ có trên iPhone 13 trở lên, các đời cũ hơn phải cầm dọc.
## Bước 6: Cập nhật iOS
Vào Cài đặt > Cài đặt chung > Cập nhật phần mềm. Một số lỗi nhận diện được Apple sửa qua bản cập nhật.
## Bước 7: Đặt lại Face ID
Vào Cài đặt > Face ID & Mật mã > **Đặt lại Face ID**, rồi đăng ký lại khuôn mặt ở nơi đủ sáng. Bước này xoá dữ liệu khuôn mặt cũ và tạo lại từ đầu, giải quyết được các trường hợp gương mặt đã thay đổi nhiều.
> Nếu máy hiện thông báo không thể kích hoạt Face ID, hoặc mục đăng ký khuôn mặt báo lỗi ngay khi mở, đừng thử thêm nữa. Đó là dấu hiệu lỗi phần cứng.
## Khi nào là lỗi phần cứng?
Các trường hợp thường gặp:
- Máy từng rơi mạnh, đặc biệt rơi đập phần trên màn hình.
- Máy từng vào nước.
- Máy từng thay màn hình hoặc sửa bo mạch ở nơi không uỷ quyền.
- Lỗi xuất hiện ngay sau một lần sửa chữa.
Cụm Face ID được ghép cặp với bo mạch của từng máy, nên sửa ở nơi không đủ thiết bị có thể làm hỏng vĩnh viễn. Đừng đưa máy cho nơi hứa sửa rẻ mà không nói rõ thay linh kiện gì.
## Mang máy tới phuonghihi
Nhân viên kỹ thuật kiểm tra và báo rõ nguyên nhân trước khi làm, xem các dịch vụ tại [trang dịch vụ](/dich-vu). Nếu máy mua tại cửa hàng và còn trong 12 tháng, đối chiếu với [chính sách bảo hành](/chinh-sach/bao-hanh): lỗi phần cứng do nhà sản xuất được bảo hành, còn rơi vỡ hoặc vào nước thì không.
BODY,
                'faqs' => [
                    ['question' => 'Vì sao Face ID tự nhiên không nhận khuôn mặt?', 'answer' => 'Thường do cụm cảm biến phía trên màn hình bị bẩn hoặc bị miếng dán, ốp lưng che. Lau sạch, tháo ốp và khởi động lại máy giải quyết được phần lớn trường hợp.'],
                    ['question' => 'Đặt lại Face ID có mất dữ liệu không?', 'answer' => 'Không. Đặt lại Face ID chỉ xoá dữ liệu khuôn mặt đã đăng ký, ảnh, tin nhắn và ứng dụng vẫn giữ nguyên.'],
                    ['question' => 'Face ID hỏng có được bảo hành không?', 'answer' => 'Được nếu là lỗi phần cứng do nhà sản xuất và máy còn trong thời hạn bảo hành. Lỗi do rơi vỡ, vào nước hoặc sửa ở nơi không uỷ quyền thì không được bảo hành.'],
                ],
            ],
            [
                'title' => 'Mua iPhone làm quà tặng: chọn máy, chọn dung lượng, chọn màu',
                'slug' => 'mua-iphone-lam-qua-tang',
                'topic' => 'tu-van',
                'status' => 'published',
                'published_at' => now()->addDays(35),
                'focus_keyword' => 'mua iphone làm quà',
                'excerpt' => 'Chọn đời máy theo người nhận, chọn dung lượng dư một chút, chọn màu an toàn, và giữ nguyên seal để món quà vẫn đổi được nếu chọn chưa đúng.',
                'body' => <<<'BODY'
Chọn máy theo người nhận chứ không theo giá: bố mẹ cần màn hình lớn và pin lâu, người trẻ cần dung lượng và camera, người đi làm cần pin và độ bền. Dung lượng nên chọn từ 128GB trở lên, màu nên chọn tông trung tính nếu không chắc sở thích. Quan trọng nhất là **đừng bóc seal và kích hoạt máy** trước khi trao quà, để còn đổi được nếu chọn chưa đúng.
## Tặng ai thì chọn máy nào?
- **Bố mẹ, ông bà**: ưu tiên màn hình lớn như các bản Plus hoặc Pro Max để chữ to, dễ nhìn. Camera không cần cao cấp, pin lâu quan trọng hơn.
- **Con đang đi học**: pin và dung lượng quan trọng hơn camera. Tham khảo cách chọn trong bài [mua iPhone cho học sinh sinh viên](/tin-tuc/mua-iphone-cho-hoc-sinh-sinh-vien).
- **Người yêu, vợ hoặc chồng**: nếu người nhận hay chụp ảnh và quay video, bản Pro là món quà đáng giá. Nếu không, bản thường đời mới là đủ.
- **Người lần đầu dùng iPhone**: chọn bản thường đời mới cho dễ làm quen, và nhớ hỏi trước người nhận có cần hỗ trợ chuyển danh bạ, ảnh từ máy cũ không.
- **Đồng nghiệp, đối tác**: chọn bản thường đời mới, màu trung tính, dung lượng vừa đủ. Món quà lịch sự mà không quá phô trương.
## Dung lượng bao nhiêu thì đủ?
Người nhận quà thường ngại nói "máy đầy bộ nhớ rồi", nên hãy chọn dư một chút. 128GB đủ cho người dùng cơ bản có bật sao lưu ảnh lên iCloud. Người hay chụp ảnh, quay video hoặc lưu nhiều tài liệu nên được tặng 256GB. Nếu không chắc, cứ chọn cao hơn một bậc: thừa dung lượng thì không ai phàn nàn, thiếu thì ngày nào cũng thấy. Cách tự tính cụ thể có trong bài [iPhone 128GB hay 256GB](/tin-tuc/iphone-128gb-hay-256gb).
## Chọn màu thế nào cho an toàn?
- Không chắc sở thích: chọn đen, trắng hoặc các tông kim loại, dễ hợp với mọi ốp lưng.
- Người nhận thích nổi bật: chọn màu đặc trưng của đời máy, thường là màu Apple dùng trong quảng cáo.
- Người lớn tuổi: tránh màu quá tối nếu họ hay để máy trong túi xách, vì khó tìm.
> Mẹo nhỏ: nếu có thể, hỏi khéo người nhận về màu ốp lưng hay màu đồng hồ họ thích. Màu điện thoại thường theo đúng gu đó.
## Nên tặng kèm gì?
Từ iPhone 12 trở đi, hộp máy không còn củ sạc. Một bộ quà trọn vẹn nên có thêm:
- Củ sạc nhanh cổng USB-C.
- Ốp lưng và miếng dán màn hình để bảo vệ máy ngay từ ngày đầu.
- Với người lớn tuổi, thêm một giá đỡ điện thoại để gọi video cho con cháu.
## Giữ quyền đổi máy nếu chọn chưa đúng
Theo [chính sách đổi trả](/chinh-sach/doi-tra) của phuonghihi, máy được đổi trả trong 7 ngày kể từ ngày nhận nếu còn nguyên seal, đủ hộp, phụ kiện và hoá đơn. Máy đã kích hoạt hoặc đã đăng nhập tài khoản thì không đổi trả được. Vì vậy:
- Giữ nguyên seal cho tới lúc trao quà.
- Nhắc người nhận kiểm tra màu và dung lượng trước khi bóc hộp.
- Giữ hoá đơn cẩn thận, bảo hành được tính theo số IMEI trên hoá đơn.
## Giao quà tận nơi
Nếu người nhận ở xa, bạn có thể đặt hàng trên website và ghi địa chỉ của người nhận. Nội thành giao trong 2 đến 4 giờ làm việc, tỉnh thành khác từ 1 đến 3 ngày. Xem máy đang có và chọn màu, dung lượng ngay tại [trang sản phẩm](/san-pham).
BODY,
                'faqs' => [
                    ['question' => 'Mua iPhone làm quà nên chọn dung lượng bao nhiêu?', 'answer' => 'Tối thiểu 128GB. Nếu người nhận hay chụp ảnh, quay video hoặc lưu nhiều tài liệu, chọn 256GB để họ không phải xoá dữ liệu sau vài tháng.'],
                    ['question' => 'Tặng iPhone rồi người nhận không thích màu thì đổi được không?', 'answer' => 'Được trong 7 ngày kể từ ngày nhận nếu máy còn nguyên seal, đủ hộp, phụ kiện và hoá đơn. Máy đã kích hoạt hoặc đã đăng nhập tài khoản thì không đổi trả được.'],
                    ['question' => 'Hộp iPhone có kèm củ sạc không?', 'answer' => 'Không. Từ iPhone 12 trở đi, hộp máy chỉ có cáp sạc, nên nên mua thêm củ sạc cổng USB-C nếu tặng quà.'],
                ],
            ],
            [
                'title' => 'Cách kiểm tra iPhone có bị khoá mạng hay không',
                'slug' => 'cach-kiem-tra-iphone-khoa-mang',
                'topic' => 'huong-dan',
                'status' => 'published',
                'published_at' => now()->addDays(38),
                'focus_keyword' => 'iphone khoá mạng',
                'excerpt' => 'Một dòng trong Cài đặt cho biết máy có khoá mạng không. Cách xem, cách thử bằng SIM của bạn, và vì sao máy dùng SIM ghép là rủi ro lâu dài.',
                'body' => <<<'BODY'
Vào Cài đặt > Cài đặt chung > Giới thiệu, kéo xuống dòng khoá nhà mạng. Nếu dòng này ghi **Không có giới hạn SIM** thì máy là bản quốc tế, dùng được mọi SIM. Nếu ghi tên một nhà mạng hoặc báo có giới hạn, máy đang bị khoá mạng. Sau đó lắp SIM của chính bạn và gọi thử một cuộc để chắc chắn.
## Khoá mạng là gì?
Ở một số nước, nhà mạng bán iPhone kèm hợp đồng cước với giá rẻ, đổi lại máy bị khoá để chỉ dùng được SIM của nhà mạng đó. Khi những chiếc máy này được mang về Việt Nam, chúng không nhận SIM trong nước nếu không có can thiệp thêm.
Khoá mạng khác hoàn toàn với khoá iCloud. Khoá iCloud là máy còn gắn với tài khoản của chủ cũ, không kích hoạt được. Cách kiểm tra khoá iCloud nằm trong bài [cách kiểm tra iPhone chính hãng](/tin-tuc/cach-kiem-tra-iphone-chinh-hang).
## Ba bước kiểm tra
- **Xem trong Cài đặt**: như hướng dẫn ở đoạn đầu, đây là cách nhanh nhất và chính xác nhất.
- **Lắp SIM của bạn**: lắp SIM đang dùng, chờ máy nhận sóng, gọi một cuộc và gửi một tin nhắn. Máy báo SIM không hợp lệ hoặc SIM không được hỗ trợ là dấu hiệu khoá mạng.
- **Nhìn khay SIM**: nếu thấy một miếng SIM mỏng dán chồng lên SIM chính, hoặc người bán dặn "đừng tháo SIM ra", đó là SIM ghép.
Nếu mua máy online, hãy yêu cầu người bán chụp màn hình dòng khoá nhà mạng trong Cài đặt trước khi chuyển tiền, rồi kiểm tra lại đúng dòng đó khi nhận máy.
> Đừng chỉ nhìn máy nhận sóng rồi yên tâm. Máy dùng SIM ghép vẫn lên sóng bình thường, chỉ có dòng trong Cài đặt mới nói thật.
## SIM ghép dùng được không?
Dùng được, nhưng rủi ro nằm ở về sau:
- Sau khi cập nhật iOS, SIM ghép có thể ngừng hoạt động, máy mất sóng cho tới khi tìm được loại SIM ghép mới.
- Một số tính năng như eSIM hoặc hai SIM có thể không dùng được.
- Khi bán lại, máy khoá mạng mất giá nhiều hơn hẳn so với bản quốc tế.
Nếu bạn chỉ định dùng máy trong thời gian ngắn và chấp nhận không cập nhật phần mềm, máy khoá mạng rẻ có thể đáng cân nhắc. Nếu bạn định dùng lâu, hãy chọn máy không khoá.
## Máy chính hãng có bị khoá mạng không?
Máy phân phối chính thức tại Việt Nam, mã máy kết thúc bằng VN/A, không khoá mạng. Máy khoá mạng thường là máy xách tay từ các thị trường có nhà mạng bán máy kèm hợp đồng. Sự khác nhau giữa hai loại máy được nói kỹ ở bài [iPhone chính hãng và xách tay khác nhau thế nào](/tin-tuc/iphone-chinh-hang-va-xach-tay-khac-nhau-the-nao).
## Máy đang dùng bị khoá mạng thì làm gì?
Một số nhà mạng nước ngoài cho phép mở khoá chính thức khi đã hết hợp đồng, nhưng thủ tục phải do chủ hợp đồng làm và thường rất khó với người mua lại. Cách thực tế nhất là giữ SIM ghép ổn định, hạn chế cập nhật iOS vội, và khi muốn đổi máy thì mang đi định giá, chấp nhận mức giá thấp hơn máy quốc tế.
## Mua máy không lo khoá mạng
Tất cả máy tại phuonghihi đều được kiểm tra dòng khoá nhà mạng trước khi lên kệ, bạn vẫn có thể lắp SIM của mình để thử ngay tại quầy. Xem máy đang có tại [trang sản phẩm](/san-pham).
BODY,
                'faqs' => [
                    ['question' => 'Xem iPhone có bị khoá mạng ở đâu?', 'answer' => 'Vào Cài đặt > Cài đặt chung > Giới thiệu, kéo xuống dòng khoá nhà mạng. Dòng này ghi Không có giới hạn SIM nghĩa là máy dùng được mọi SIM.'],
                    ['question' => 'iPhone khoá mạng có dùng được ở Việt Nam không?', 'answer' => 'Dùng được nếu gắn thêm SIM ghép, nhưng SIM ghép có thể ngừng hoạt động sau khi cập nhật iOS và máy khoá mạng mất giá nhiều hơn khi bán lại.'],
                    ['question' => 'Khoá mạng và khoá iCloud khác nhau thế nào?', 'answer' => 'Khoá mạng chỉ giới hạn SIM được dùng. Khoá iCloud là máy còn gắn với tài khoản của chủ cũ, không kích hoạt được và chỉ chủ tài khoản mới gỡ được.'],
                ],
            ],
            [
                'title' => 'Màn hình, camera hay pin: ưu tiên gì khi ngân sách hẹp?',
                'slug' => 'chon-iphone-ngan-sach-hep-man-hinh-camera-hay-pin',
                'topic' => 'so-sanh',
                'status' => 'published',
                'published_at' => now()->addDays(42),
                'focus_keyword' => 'chọn iphone ngân sách hẹp',
                'excerpt' => 'Không mua được máy mạnh nhất ở mọi mặt thì chọn theo thứ bạn dùng nhiều nhất. Thứ tự ưu tiên cho bốn kiểu người dùng và cách đánh đổi hợp lý.',
                'body' => <<<'BODY'
Với phần lớn người dùng, thứ tự nên ưu tiên là pin, rồi dung lượng, rồi camera, cuối cùng mới đến màn hình. Pin là thứ bạn cảm nhận mỗi ngày, còn camera và màn hình cao cấp là phần đội giá nhanh nhất. Nếu bạn chụp ảnh là chính hoặc xem video nhiều, hãy đảo thứ tự theo đúng thói quen của mình.
## Vì sao pin thường đứng đầu?
Một chiếc máy camera đẹp nhưng hết pin lúc 4 giờ chiều thì bạn sẽ khó chịu mỗi ngày. Pin cũng là thứ xuống cấp theo thời gian, nên máy có pin lớn ngay từ đầu sẽ còn dùng được lâu hơn trước khi phải thay. Với máy đã qua sử dụng, luôn kiểm tra dung lượng pin tối đa theo hướng dẫn ở bài [cách kiểm tra pin iPhone](/tin-tuc/cach-kiem-tra-pin-iphone).
Một cách tự kiểm tra: nhớ lại tuần vừa rồi, bạn khó chịu vì máy hết pin, vì hết bộ nhớ hay vì ảnh chụp không đẹp bao nhiêu lần. Thứ làm bạn khó chịu nhiều nhất chính là thứ nên ưu tiên khi chọn máy mới.
## Bốn kiểu người dùng, bốn thứ tự ưu tiên
- **Dùng cơ bản** (nhắn tin, gọi điện, mạng xã hội): pin, dung lượng, độ bền. Camera và màn hình đời cũ vẫn thừa đủ.
- **Chụp ảnh nhiều** (con nhỏ, du lịch, bán hàng online): camera, dung lượng, pin. Đây là trường hợp đáng bỏ thêm tiền cho bản Pro đời trước.
- **Xem phim, đọc nhiều**: màn hình lớn, pin, dung lượng. Bản Plus hoặc Pro Max đời cũ thường hợp hơn bản thường đời mới.
- **Chơi game**: chip đời mới, tản nhiệt, pin. Chọn đời máy càng mới càng tốt, hạ dung lượng nếu cần.
## Đời mới bản thường hay đời cũ bản cao cấp?
Đây là đánh đổi phổ biến nhất khi ngân sách hẹp. Quy tắc chung:
- Chọn **đời mới bản thường** nếu bạn muốn máy được cập nhật phần mềm lâu nhất và dùng từ 4 năm trở lên.
- Chọn **đời cũ bản cao cấp** nếu bạn cần camera hoặc màn hình tốt ngay bây giờ và định đổi máy sau 2 đến 3 năm.
Riêng câu hỏi có nên trả thêm cho bản Pro Max, xem bài [iPhone Pro Max có đáng tiền hơn Pro không](/tin-tuc/iphone-pro-max-co-dang-tien-hon-pro).
> Một chiếc máy đời cũ hơn một năm nhưng đúng thứ bạn cần gần như luôn làm bạn hài lòng hơn máy đời mới nhất mà thiếu đúng thứ đó.
## Những thứ có thể bỏ qua khi ngân sách hẹp
- **Tần số quét màn hình cao**: mượt hơn thật, nhưng bạn quen với màn hình thường chỉ sau vài ngày.
- **Ống kính tele**: chỉ cần nếu bạn hay chụp xa, chụp chân dung chuyên nghiệp.
- **Màu mới nhất của đời máy**: thường giá cao hơn mà cấu hình giống hệt.
- **Dung lượng quá lớn**: từ 512GB trở lên hiếm khi cần với người dùng phổ thông.
## Thứ không nên cắt dù ngân sách hẹp
Dung lượng. Mọi thứ khác trên iPhone đều có cách bù: pin thay được, camera đời cũ vẫn chụp tốt khi đủ sáng. Riêng bộ nhớ thì không nâng cấp được, chọn thiếu là khó chịu suốt thời gian dùng máy.
## Bắt đầu từ ngân sách của bạn
Xác định số tiền tối đa trước, rồi lọc máy theo tầm giá ở [trang sản phẩm](/san-pham). Nếu chưa biết nên đặt ngân sách bao nhiêu, bài [nên mua iPhone nào](/tin-tuc/nen-mua-iphone-nao-2026) chia sẵn bốn mức với gợi ý máy cụ thể cho từng mức.
BODY,
                'faqs' => [
                    ['question' => 'Ngân sách hẹp nên ưu tiên pin hay camera?', 'answer' => 'Với người dùng phổ thông, nên ưu tiên pin vì đó là thứ cảm nhận mỗi ngày. Chỉ ưu tiên camera nếu bạn chụp ảnh là nhu cầu chính, ví dụ bán hàng online hoặc hay đi du lịch.'],
                    ['question' => 'Nên mua iPhone đời mới bản thường hay đời cũ bản Pro?', 'answer' => 'Chọn đời mới bản thường nếu muốn dùng lâu và được cập nhật phần mềm lâu nhất. Chọn đời cũ bản Pro nếu cần camera tốt ngay và định đổi máy sau hai đến ba năm.'],
                    ['question' => 'Có nên giảm dung lượng để mua đời máy mới hơn không?', 'answer' => 'Không nên giảm xuống dưới mức bạn cần. Dung lượng iPhone không nâng cấp được sau khi mua, trong khi pin có thể thay và camera đời cũ vẫn đủ dùng.'],
                ],
            ],
            [
                'title' => 'Bảo hành iPhone tại phuonghihi: được gì, không được gì?',
                'slug' => 'chinh-sach-bao-hanh-iphone-tai-phuonghihi',
                'topic' => 'tin-moi',
                'status' => 'published',
                'published_at' => now()->addDays(45),
                'focus_keyword' => 'bảo hành iphone',
                'excerpt' => 'Bảo hành 12 tháng theo IMEI, đổi máy mới trong 30 ngày đầu nếu lỗi do nhà sản xuất, và những trường hợp không được bảo hành. Viết dạng hỏi đáp.',
                'body' => <<<'BODY'
Mọi iPhone mua tại phuonghihi được bảo hành 12 tháng kể từ ngày mua, tính theo số IMEI trên hoá đơn. Trong 30 ngày đầu, nếu máy lỗi phần cứng do nhà sản xuất, cửa hàng đổi máy mới cùng model và cấu hình, không mất phí. Rơi vỡ, vào nước và máy đã sửa ở nơi không uỷ quyền thì không được bảo hành.
## Bảo hành bao lâu và tính từ khi nào?
12 tháng, tính từ ngày mua ghi trên hoá đơn. Thời hạn gắn với số IMEI của máy nên bạn không cần giữ phiếu bảo hành giấy: nhân viên tra được tại quầy hoặc qua hotline chỉ với số IMEI. Cách xem số IMEI là bấm gọi *#06# trên máy.
## Đổi máy mới trong 30 ngày đầu là thế nào?
Nếu máy phát sinh lỗi phần cứng do nhà sản xuất trong 30 ngày đầu kể từ ngày mua, cửa hàng đổi cho bạn máy mới cùng model, cùng cấu hình mà không phát sinh chi phí. Ví dụ: loa rè, camera không lấy nét, máy tự khởi động lại liên tục dù chưa rơi hay vào nước.
> Chính sách 1 đổi 1 trong 30 ngày khác với đổi trả trong 7 ngày. Đổi trả trong 7 ngày áp dụng khi máy còn nguyên seal và bạn muốn đổi ý, còn 1 đổi 1 áp dụng khi máy đã dùng và bị lỗi do nhà sản xuất.
Điều kiện đổi trả khi còn nguyên seal nằm ở [chính sách đổi trả](/chinh-sach/doi-tra).
## Những trường hợp được bảo hành
- Lỗi phần cứng phát sinh trong quá trình sử dụng bình thường, do nhà sản xuất.
- Máy còn nguyên trạng, không bị can thiệp bởi bên thứ ba ngoài hệ thống bảo hành uỷ quyền.
- Số IMEI trên máy trùng khớp với số IMEI trên hoá đơn.
## Những trường hợp không được bảo hành
- Rơi vỡ, cấn móp, vào nước, cháy nổ do tác động từ bên ngoài.
- Máy đã sửa chữa hoặc thay linh kiện ở nơi không được uỷ quyền.
- Pin hao mòn tự nhiên sau thời gian dài sử dụng. Trường hợp này áp dụng chính sách bảo hành pin riêng theo quy định của Apple.
Nếu máy của bạn không còn được bảo hành, vẫn có thể sửa có tính phí, xem các dịch vụ tại [trang dịch vụ](/dich-vu).
## Gửi máy bảo hành thế nào?
- Mang máy đến cửa hàng, hoặc gửi qua đơn vị vận chuyển kèm hoá đơn hoặc mã đơn hàng.
- Cửa hàng kiểm tra và báo tình trạng máy trong vòng 24 đến 48 giờ làm việc.
- Sửa chữa hoặc đổi máy, rồi bàn giao lại theo lịch hẹn.
Trước khi gửi, hãy sao lưu dữ liệu và ghi lại mật khẩu Apple Account, vì một số trường hợp cần khôi phục máy.
## Làm gì để không mất quyền bảo hành?
- Đừng mang máy đi sửa ở nơi không uỷ quyền khi còn trong thời hạn, kể cả lỗi nhỏ.
- Dùng ốp lưng và miếng dán màn hình để tránh rơi vỡ, vốn là lý do từ chối bảo hành phổ biến nhất.
- Không tự mở máy hoặc tháo vít, kể cả chỉ để vệ sinh bên trong.
- Giữ hoá đơn hoặc mã đơn hàng để đối chiếu nhanh khi cần.
Một số lỗi thường gặp có thể tự xử lý trước khi mang đi, ví dụ lỗi nhận diện khuôn mặt, xem bài [Face ID không hoạt động](/tin-tuc/face-id-khong-hoat-dong). Nội dung đầy đủ của chính sách luôn được cập nhật tại [trang chính sách bảo hành](/chinh-sach/bao-hanh).
BODY,
                'faqs' => [
                    ['question' => 'iPhone mua tại phuonghihi được bảo hành bao lâu?', 'answer' => '12 tháng kể từ ngày mua, tính theo số IMEI trên hoá đơn. Thời hạn tra được tại quầy hoặc qua hotline, không cần giữ phiếu bảo hành giấy.'],
                    ['question' => 'iPhone lỗi trong tháng đầu có được đổi máy mới không?', 'answer' => 'Có. Nếu máy lỗi phần cứng do nhà sản xuất trong 30 ngày đầu kể từ ngày mua, phuonghihi đổi máy mới cùng model và cấu hình, không mất phí.'],
                    ['question' => 'iPhone rơi vỡ hoặc vào nước có được bảo hành không?', 'answer' => 'Không. Rơi vỡ, cấn móp, vào nước và cháy nổ do tác động bên ngoài nằm ngoài phạm vi bảo hành, nhưng vẫn có thể sửa có tính phí tại cửa hàng.'],
                ],
            ],
            [
                'title' => 'Cách xoá sạch dữ liệu iPhone trước khi bán',
                'slug' => 'cach-xoa-sach-du-lieu-iphone-truoc-khi-ban',
                'topic' => 'huong-dan',
                'status' => 'published',
                'published_at' => now()->addDays(49),
                'focus_keyword' => 'xoá dữ liệu iphone',
                'excerpt' => 'Năm bước theo đúng thứ tự để xoá sạch iPhone trước khi bán: sao lưu, huỷ ghép nối, đăng xuất Apple Account, xoá máy và gỡ khỏi danh sách thiết bị.',
                'body' => <<<'BODY'
Sao lưu trước, rồi đăng xuất Apple Account để tắt Tìm iPhone, sau đó vào Cài đặt > Cài đặt chung > Chuyển hoặc Đặt lại iPhone > Xoá tất cả nội dung và cài đặt. Làm đúng thứ tự này, máy trở về trạng thái như mới xuất xưởng và người mua kích hoạt được ngay. Bỏ bước đăng xuất thì máy vẫn bị khoá vào tài khoản của bạn.
## Bước 1: Sao lưu dữ liệu
Xoá rồi là không lấy lại được. Làm theo bài [cách sao lưu iPhone](/tin-tuc/cach-sao-luu-iphone) và kiểm tra bản sao lưu đã hoàn tất trước khi đi tiếp. Nhớ chuyển ứng dụng tạo mã xác thực hai lớp sang máy mới nếu bạn có dùng.
## Bước 2: Huỷ ghép nối các thiết bị đi kèm
- **Apple Watch**: mở ứng dụng Watch trên iPhone, chọn đồng hồ và bấm huỷ ghép nối. Đồng hồ sẽ tự sao lưu lên iPhone trước khi huỷ.
- **Tai nghe AirPods**: không bắt buộc, nhưng nên quên thiết bị trong phần Bluetooth để tránh người mua thấy tên tai nghe của bạn.
## Bước 3: Đăng xuất Apple Account
Vào Cài đặt > [tên của bạn], kéo xuống cuối và bấm **Đăng xuất**. Máy sẽ hỏi mật khẩu Apple Account để tắt Tìm iPhone. Đây là bước quan trọng nhất: máy còn bật Tìm iPhone thì người mua không kích hoạt được, và cửa hàng thu cũ cũng không nhận.
> Nếu bạn chuyển sang điện thoại Android, hãy tắt iMessage trong Cài đặt > Tin nhắn trước khi đăng xuất. Nếu không, tin nhắn từ người dùng iPhone khác có thể không tới máy mới của bạn.
## Bước 4: Xoá tất cả nội dung và cài đặt
Vào Cài đặt > Cài đặt chung > Chuyển hoặc Đặt lại iPhone > **Xoá tất cả nội dung và cài đặt**. Máy sẽ hỏi mật mã máy và có thể hỏi lại mật khẩu Apple Account. Nếu bạn dùng eSIM, máy sẽ hỏi có giữ hay xoá eSIM: chọn xoá khi bán cho người khác, rồi liên hệ nhà mạng để chuyển eSIM sang máy mới của bạn.
Trước khi bấm xoá, mở lại Ảnh và Tin nhắn trên máy mới một lần nữa để chắc chắn dữ liệu đã có đủ. Đừng thay bước này bằng cách khôi phục máy qua máy tính khi chưa đăng xuất tài khoản, vì máy vẫn sẽ bị khoá kích hoạt sau khi khôi phục.
Quá trình xoá mất vài phút. Khi xong, máy hiện màn hình chào **Xin chào** như máy mới.
## Bước 5: Gỡ máy khỏi danh sách thiết bị
Trên máy mới hoặc máy tính, đăng nhập Apple Account và xem danh sách thiết bị. Nếu chiếc máy vừa bán vẫn còn trong danh sách, gỡ nó ra. Bước này đảm bảo máy cũ không còn liên quan gì tới tài khoản của bạn.
## Đừng quên SIM và phụ kiện
- Tháo SIM vật lý ra khỏi khay.
- Gom đủ hộp, cáp và sổ hướng dẫn nếu còn giữ, máy đủ hộp thường được giá hơn.
- Lau sạch máy và bóc miếng dán màn hình đã trầy để người mua thấy đúng tình trạng.
## Mang máy đi thu cũ đổi mới
Máy đã xoá sạch và đăng xuất tài khoản là máy sẵn sàng định giá. Ước tính trước giá thu tại [trang thu cũ đổi mới](/thu-cu-doi-moi), rồi mang máy tới cửa hàng để chốt giá chính xác. Các yếu tố làm tăng hay giảm giá máy có trong bài [thu cũ đổi mới iPhone được định giá thế nào](/tin-tuc/thu-cu-doi-moi-iphone-dinh-gia-the-nao).
BODY,
                'faqs' => [
                    ['question' => 'Xoá tất cả nội dung và cài đặt có xoá sạch dữ liệu iPhone không?', 'answer' => 'Có. Thao tác này đưa máy về trạng thái như mới xuất xưởng. Nhưng phải đăng xuất Apple Account trước, nếu không máy vẫn bị khoá vào tài khoản của bạn.'],
                    ['question' => 'Quên đăng xuất iCloud trước khi bán thì sao?', 'answer' => 'Người mua sẽ không kích hoạt được máy. Bạn có thể gỡ máy từ xa bằng cách đăng nhập Apple Account trên thiết bị khác, vào Tìm và xoá máy khỏi danh sách thiết bị.'],
                    ['question' => 'Có cần tháo SIM trước khi bán iPhone không?', 'answer' => 'Có. Tháo SIM vật lý ra khỏi khay, và nếu dùng eSIM thì chọn xoá eSIM khi đặt lại máy, rồi liên hệ nhà mạng để chuyển sang máy mới.'],
                ],
            ],
            [
                'title' => '5 lỗi thường gặp khi mua iPhone lần đầu',
                'slug' => '5-loi-thuong-gap-khi-mua-iphone-lan-dau',
                'topic' => 'tu-van',
                'status' => 'published',
                'published_at' => now()->addDays(52),
                'focus_keyword' => 'mua iphone lần đầu',
                'excerpt' => 'Chọn theo đời máy thay vì nhu cầu, thiếu dung lượng, không kiểm tra lúc nhận, chỉ hỏi tiền góp mỗi tháng, quên mật khẩu Apple Account. Tránh cả năm lỗi.',
                'body' => <<<'BODY'
Năm lỗi hay gặp nhất khi mua iPhone lần đầu là: chọn máy theo đời mới nhất thay vì theo nhu cầu, chọn thiếu dung lượng, không kiểm tra máy lúc nhận, chỉ hỏi số tiền góp mỗi tháng, và quên hoặc không ghi lại mật khẩu Apple Account. Lỗi nào cũng tránh được nếu bạn biết trước.
## Lỗi 1: Chọn đời máy mới nhất thay vì chọn theo nhu cầu
Người mua lần đầu thường nghĩ máy mới nhất là máy tốt nhất cho mình. Thực tế, với nhu cầu nhắn tin, mạng xã hội và chụp ảnh đời thường, máy đời trước hai đến ba năm vẫn dư sức, trong khi rẻ hơn đáng kể. Số tiền tiết kiệm được nên dùng để lên dung lượng hoặc mua phụ kiện bảo vệ.
Cách tránh: xác định ngân sách và nhu cầu trước, rồi mới xem máy. Bài [nên mua iPhone nào](/tin-tuc/nen-mua-iphone-nao-2026) chia sẵn bốn mức ngân sách với gợi ý cụ thể.
## Lỗi 2: Chọn thiếu dung lượng để tiết kiệm
Đây là lỗi gây khó chịu lâu nhất, vì dung lượng iPhone không nâng cấp được sau khi mua. Nhiều người chọn bản thấp nhất để tiết kiệm vài triệu, rồi sau một năm phải xoá ảnh mỗi tuần hoặc trả phí iCloud hằng tháng.
Cách tránh: tự tính dung lượng cần dùng trong hai phút theo bài [iPhone 128GB hay 256GB](/tin-tuc/iphone-128gb-hay-256gb).
## Lỗi 3: Không kiểm tra máy khi nhận
Nhiều người nhận máy, ký nhận rồi mới bóc hộp ở nhà. Nếu có vấn đề về ngoại hình hoặc phụ kiện, lúc đó rất khó chứng minh.
Cách tránh: kiểm tra ngay khi nhận, đặc biệt với máy đã qua sử dụng. Sáu bước kiểm tra trong năm phút có ở bài [cách kiểm tra iPhone chính hãng](/tin-tuc/cach-kiem-tra-iphone-chinh-hang). Với đơn thanh toán khi nhận hàng, bạn được mở hộp kiểm tra ngoại hình và phụ kiện trước khi trả tiền.
> Máy mới còn nguyên seal thì đừng vội bóc nếu chưa chắc chắn về màu và dung lượng. Máy đã kích hoạt không còn được đổi trả theo chính sách 7 ngày.
## Lỗi 4: Chỉ hỏi số tiền trả góp mỗi tháng
"Mỗi tháng góp bao nhiêu" là câu hỏi đầu tiên của hầu hết người mua trả góp, nhưng lại là câu ít quan trọng nhất. Hai gói cùng mức góp mỗi tháng có thể chênh nhau vài triệu ở tổng số tiền phải trả.
Cách tránh: luôn hỏi tổng số tiền đến hết kỳ, phí chuyển đổi hoặc phí hồ sơ, và phạt trả chậm. So sánh hai hình thức trả góp phổ biến trong bài [trả góp qua thẻ tín dụng hay công ty tài chính](/tin-tuc/tra-gop-the-tin-dung-hay-cong-ty-tai-chinh).
## Lỗi 5: Không ghi lại mật khẩu Apple Account
Khi kích hoạt máy lần đầu, bạn sẽ tạo hoặc đăng nhập Apple Account. Nhiều người nhờ người bán tạo giúp, hoặc tự tạo rồi quên mật khẩu. Đến lúc đổi máy, bán máy hay khôi phục dữ liệu thì không đăng xuất được, máy bị khoá vào tài khoản.
Cách tránh:
- Tự tạo Apple Account bằng email và số điện thoại chính chủ của bạn.
- Ghi mật khẩu ở nơi an toàn, không để người khác giữ.
- Bật sao lưu iCloud ngay từ ngày đầu.
## Mua lần đầu tại phuonghihi
Nếu mua tại cửa hàng, nhân viên sẽ hướng dẫn bạn kích hoạt máy và tự tạo Apple Account bằng thông tin của chính mình, để tài khoản chỉ bạn nắm giữ. Xem máy đang bán tại [trang sản phẩm](/san-pham), và đọc trước [chính sách đổi trả](/chinh-sach/doi-tra) để biết quyền lợi của mình.
BODY,
                'faqs' => [
                    ['question' => 'Lần đầu mua iPhone nên chọn máy mới nhất không?', 'answer' => 'Không nhất thiết. Với nhu cầu nhắn tin, mạng xã hội và chụp ảnh đời thường, máy đời trước hai đến ba năm vẫn dư sức và rẻ hơn đáng kể.'],
                    ['question' => 'Có nên nhờ người bán tạo Apple Account giúp không?', 'answer' => 'Không nên. Hãy tự tạo Apple Account bằng email và số điện thoại chính chủ, rồi ghi mật khẩu ở nơi an toàn. Quên mật khẩu thì không đăng xuất được khi bán hoặc đổi máy.'],
                    ['question' => 'Nhận iPhone rồi mới phát hiện sai màu thì đổi được không?', 'answer' => 'Được nếu máy còn nguyên seal, đủ hộp, phụ kiện và hoá đơn, trong 7 ngày kể từ ngày nhận. Giao sai màu hoặc sai dung lượng so với đơn đặt hàng thì được hoàn tiền 100%.'],
                ],
            ],
            [
                'title' => 'Giao hàng và thanh toán tại phuonghihi: COD, VNPay, trả góp',
                'slug' => 'giao-hang-va-thanh-toan-tai-phuonghihi',
                'topic' => 'tin-moi',
                'status' => 'published',
                'published_at' => now()->addDays(56),
                'focus_keyword' => 'thanh toán mua iphone',
                'excerpt' => 'Phí giao hàng đồng giá, thời gian giao theo khu vực, ba cách thanh toán và điều kiện của từng cách, cùng cách tra cứu đơn hàng và nhận hoàn tiền nếu cần.',
                'body' => <<<'BODY'
Mua iPhone tại phuonghihi, bạn có ba cách trả tiền: thanh toán khi nhận hàng, thanh toán online qua VNPay, hoặc trả góp cho đơn từ 3 triệu đồng. Phí giao hàng đồng giá 30.000đ toàn quốc, hiện rõ trước khi đặt. Nội thành nhận máy trong 2 đến 4 giờ làm việc, tỉnh thành khác trong 1 đến 3 ngày.
## Phí giao hàng bao nhiêu?
Đồng giá 30.000đ cho mọi địa chỉ trên toàn quốc. Phí này hiện ở bước thanh toán trước khi bạn bấm đặt hàng, và không phát sinh thêm bất kỳ khoản nào khi nhận máy.
## Bao lâu thì nhận được máy?
- **Nội thành**: 2 đến 4 giờ làm việc kể từ khi cửa hàng xác nhận đơn.
- **Tỉnh thành khác**: 1 đến 3 ngày làm việc qua đối tác vận chuyển.
Chi tiết đầy đủ có ở [chính sách vận chuyển và thanh toán](/chinh-sach/van-chuyen).
## Cách 1: Thanh toán khi nhận hàng (COD)
Bạn trả tiền mặt trực tiếp cho nhân viên giao hàng. Với đơn COD, bạn được mở hộp kiểm tra ngoại hình và phụ kiện đi kèm trước khi trả tiền. Đây là lựa chọn an tâm nhất cho người mua online lần đầu.
> Với máy mới còn nguyên seal, kiểm tra ngoại hình hộp, seal và phụ kiện là đủ. Đừng kích hoạt máy ngay trước mặt nhân viên giao hàng nếu chưa chắc chắn về màu và dung lượng, vì máy đã kích hoạt không còn được đổi trả theo chính sách 7 ngày.
## Cách 2: Thanh toán online qua VNPay
Ở bước thanh toán, chọn VNPay và trả bằng một trong ba cách:
- Thẻ ATM nội địa có đăng ký thanh toán trực tuyến.
- Thẻ quốc tế Visa hoặc Mastercard.
- Quét mã QR bằng ứng dụng ngân hàng.
Giao dịch được xử lý trên cổng thanh toán của VNPay, cửa hàng không lưu thông tin thẻ của bạn. Thanh toán xong, bạn được chuyển về trang xác nhận đơn hàng. Chọn cách này khi không có sẵn tiền mặt, hoặc khi người nhận máy không phải là bạn. Nếu đơn bị huỷ trước khi giao, tiền được hoàn 100% về đúng phương thức đã thanh toán trong 3 đến 7 ngày làm việc.
## Cách 3: Trả góp
Áp dụng cho đơn từ 3.000.000đ, qua thẻ tín dụng Visa hoặc Mastercard, hoặc qua công ty tài chính liên kết. Không cần chứng minh thu nhập và được xét duyệt trong ngày. Tính trước số tiền mỗi tháng ở [trang trả góp](/tra-gop), rồi liên hệ cửa hàng để làm hồ sơ.
## Theo dõi đơn hàng thế nào?
Sau khi đặt, bạn nhận được mã đơn hàng. Nhập mã này tại [trang tra cứu đơn hàng](/don-hang/tra-cuu) để xem đơn đang ở bước nào. Nếu có tài khoản, toàn bộ đơn đã đặt nằm trong mục Đơn hàng của tôi.
## Muốn đổi trả hoặc hoàn tiền thì sao?
Máy còn nguyên seal, đủ hộp, phụ kiện và hoá đơn được đổi trả trong 7 ngày kể từ ngày nhận. Giao sai sản phẩm, sai màu, sai dung lượng hoặc lỗi ngay khi mở hộp thì được hoàn tiền 100%. Gửi yêu cầu kèm mã đơn hàng, cửa hàng phản hồi trong 24 giờ làm việc. Điều kiện chi tiết ở [chính sách đổi trả](/chinh-sach/doi-tra).
## Sẵn sàng đặt hàng?
Chọn máy, màu và dung lượng tại [trang sản phẩm](/san-pham), thêm vào giỏ và chọn cách thanh toán phù hợp ở bước cuối. Nếu còn phân vân, bạn có thể gọi hotline hoặc ghé cửa hàng để được tư vấn trực tiếp trước khi đặt.
BODY,
                'faqs' => [
                    ['question' => 'Phí giao iPhone tại phuonghihi là bao nhiêu?', 'answer' => 'Đồng giá 30.000đ toàn quốc. Phí hiện rõ ở bước thanh toán trước khi đặt hàng và không phát sinh thêm khi nhận máy.'],
                    ['question' => 'Mua iPhone COD có được kiểm tra máy trước khi trả tiền không?', 'answer' => 'Có. Với đơn thanh toán khi nhận hàng, bạn được mở hộp kiểm tra ngoại hình và phụ kiện đi kèm trước khi trả tiền cho nhân viên giao hàng.'],
                    ['question' => 'Thanh toán VNPay rồi huỷ đơn thì hoàn tiền thế nào?', 'answer' => 'Nếu đơn bị huỷ trước khi giao, tiền được hoàn 100% về đúng phương thức đã thanh toán trong 3 đến 7 ngày làm việc.'],
                ],
            ],
        ];
    }
}
