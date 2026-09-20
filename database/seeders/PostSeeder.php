<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;

/**
 * The six starter articles of the content plan — two per week for the
 * first three weeks. Each one follows the same SEO shape the admin form
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
                $post + [
                    'status' => 'published',
                    // Oldest first, one post every three days, so the
                    // storefront listing is not six posts on one date.
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
        ];
    }
}
