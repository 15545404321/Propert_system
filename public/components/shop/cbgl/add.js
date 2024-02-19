Vue.component('Add', {
	template: `
		<el-dialog title="生成抄表" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width=" ismobile()?'90px':'16%'">
				<el-row >
					<el-col :span="24">
						<el-form-item label="财务月份" prop="cbgl_cwyf">
							<el-date-picker value-format="yyyy-MM" type="month" v-model="form.cbgl_cwyf" clearable placeholder="请输入财务月份"></el-date-picker>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="开始时间" prop="cbgl_kstime">
							<el-date-picker value-format="yyyy-MM-dd" type="date" v-model="form.cbgl_kstime" clearable placeholder="请输入开始时间"></el-date-picker>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="结束时间" prop="cbgl_jstime">
							<el-date-picker value-format="yyyy-MM-dd" type="date" v-model="form.cbgl_jstime" clearable placeholder="请输入结束时间"></el-date-picker>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="抄表楼宇" prop="louyu_id">
							<treeselect v-if="show" :appendToBody="true" :default-expand-level="2" v-model="form.louyu_id" :options="louyu_ids" :normalizer="normalizer" :show-count="true" zIndex="999999" placeholder="请选择抄表楼宇"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="仪表类型" prop="yblx_id">
							<el-select   style="width:100%" v-model="form.yblx_id" filterable clearable placeholder="请选择仪表类型">
								<el-option v-for="(item,i) in yblx_ids" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="仪表种类" prop="ybzl_id">
							<el-select   style="width:100%" v-model="form.ybzl_id" filterable clearable placeholder="请选择仪表种类">
								<el-option v-for="(item,i) in ybzl_ids" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
			</el-form>
			<div slot="footer" class="dialog-footer">
				<el-button :size="size" :loading="loading" type="primary" @click="submit" >
					<span v-if="!loading">确 定</span>
					<span v-else>提 交 中...</span>
				</el-button>
				<el-button :size="size" @click="closeForm">取 消</el-button>
			</div>
		</el-dialog>
	`
	,
	components:{
		'treeselect':VueTreeselect.Treeselect,
	},
	props: {
		show: {
			type: Boolean,
			default: false
		},
		size: {
			type: String,
			default: 'small'
		},
	},
	data(){
		return {
			form: {
				shop_id:'',
				xqgl_id:'',
				cbgl_cwyf:'',
				cbgl_kstime:'',
				cbgl_jstime:'',
				yblx_id:'',
				ybzl_id:'',
				cbpc_id:'',
			},
			member_ids:[],
			fcxx_ids:[],
			louyu_ids:[],
			yblx_ids:[],
			ybzl_ids:[],
			loading:false,
			rules: {
			}
		}
	},
	watch:{
		show(val){
			if(val){
				axios.post(base_url + '/Cbgl/getFieldList').then(res => {
					if(res.data.status == 200){
						this.member_ids = res.data.data.member_ids
						this.fcxx_ids = res.data.data.fcxx_ids
						this.louyu_ids = res.data.data.louyu_ids
						this.yblx_ids = res.data.data.yblx_ids
						this.ybzl_ids = res.data.data.ybzl_ids
					}
				})
			}
		}
	},
	methods: {
		open(){
			const myURL = new URL(window.location.href)
			const urlobj = param2Obj(myURL.href)
			this.form.cbpc_id = urlobj.cbpc_id
		},
		submit(){
			this.$refs['form'].validate(valid => {
				if(valid) {
					this.loading = true
					axios.post(base_url + '/Cbgl/add',this.form).then(res => {
						if(res.data.status == 200){
							this.$message({message: res.data.msg, type: 'success'})
							this.$emit('refesh_list')
							this.closeForm()
						}else{
							this.loading = false
							this.$message.error(res.data.msg)
						}
					}).catch(()=>{
						this.loading = false
					})
				}
			})
		},
		normalizer(node) {
			if (node.children && !node.children.length) {
				delete node.children
			}
			return {
				id: node.val,
				label: node.key,
				children: node.children
			}
		},
		closeForm(){
			this.$emit('update:show', false)
			this.loading = false
			if (this.$refs['form']!==undefined) {
				this.$refs['form'].resetFields()
			}
		},
	}
})
