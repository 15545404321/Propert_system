Vue.component('Detail', {
	template: `
		<el-dialog title="查看详情" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<table cellpadding="0" cellspacing="0" class="table table-bordered" align="center" width="100%" style="word-break:break-all; margin-bottom:30px;  font-size:13px;">
				<tbody>
					<tr>
						<td class="title" width="100">楼宇名称：</td>
						<td>
							{{form.louyu_name}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">楼宇类型：</td>
						<td>
							{{form.louyutype_name}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">楼房属性：</td>
						<td>
							{{form.louyusx_name}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">单元数量：</td>
						<td>
							{{form.louyu_dysl}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">楼层总数：</td>
						<td>
							{{form.louyu_lczs}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">层户总数：</td>
						<td>
							{{form.louyu_chzs}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">负楼层数：</td>
						<td>
							{{form.louyu_flcs}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">建筑面积：</td>
						<td>
							{{form.louyu_jzmj}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">建设单位：</td>
						<td>
							{{form.louyu_jsdw}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">电梯数量：</td>
						<td>
							{{form.louyu_dtsl}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">库商层数：</td>
						<td>
							{{form.louyu_dscs}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">一层几户：</td>
						<td>
							{{form.louyu_ycjh}}
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
			axios.post(base_url+'/Louyu/detail',this.info).then(res => {
				this.form = res.data.data
			})
		},
		closeForm(){
			this.$emit('update:show', false)
		}
	}
})
