Vue.component('Detail', {
	template: `
		<el-dialog title="查看详情" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<table cellpadding="0" cellspacing="0" class="table table-bordered" align="center" width="100%" style="word-break:break-all; margin-bottom:30px;  font-size:13px;">
				<tbody>
					<tr>
						<td class="title" width="100">导航名称：</td>
						<td>
							{{form.title}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">排序字段：</td>
						<td>
							{{form.sort}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">菜单类型：</td>
						<td>
							<span v-if="form.type == '0'">顶级菜单</span>
							<span v-if="form.type == '1'">跳转链接</span>
							<span v-if="form.type == '2'">点击内容</span>
							<span v-if="form.type == '3'">跳转小程序</span>
							<span v-if="form.type == '4'">展示图片</span>
						</td>
					</tr>
					<tr>
						<td class="title" width="100">跳转url：</td>
						<td>
							{{form.url}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">小程序appid：</td>
						<td>
							{{form.xcx_appid}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">小程序url：</td>
						<td>
							{{form.xcx_url}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">点击code：</td>
						<td>
							{{form.cont_code}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">素材id：</td>
						<td>
							{{form.media_id}}
						</td>
					</tr>
				</tbody>
			</table>
		</el-dialog>
	`
	,
	props: {
		show: {
			type: Boolean,
			default: true
		},
		size: {
			type: String,
			default: 'mini'
		},
		info: {
			type: Object,
		},
	},
	data() {
		return {
			form:{
			},
		}
	},
	methods: {
		open(){
			axios.post(base_url+'/ShopWxConfig/detail',this.info).then(res => {
				this.form = res.data.data
			})
		},
		closeForm(){
			this.$emit('update:show', false)
		}
	}
})
