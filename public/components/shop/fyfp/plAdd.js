Vue.component('Pladd', {
	template: `
		<el-dialog title="批量分配" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width=" ismobile()?'90px':'16%'">
				<el-row >
					<el-col :span="24">
						<el-form-item label="楼宇/单元" prop="louyu_id">
							<treeselect v-if="show" :appendToBody="true" :default-expand-level="2" v-model="form.louyu_id" :options="louyu_ids" :normalizer="normalizer" :show-count="true" zIndex="999999" placeholder="请选择楼宇/单元"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="房屋类型" prop="fwlx_id">
							<el-select   style="width:100%" v-model="form.fwlx_id" filterable clearable placeholder="请选择房屋类型">
								<el-option v-for="(item,i) in fwlx_ids" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
				<el-row >
					<el-form-item label="正负楼层" prop="zheng_fu">
						<el-radio-group v-model="form.zheng_fu">
							<el-radio :label="1">正楼层</el-radio>
							<el-radio :label="2">负楼层</el-radio>
						</el-radio-group>
					</el-form-item>
				</el-row>
				<el-col :span="24">
					<el-form-item label="开始楼层" prop="start_loucen">
						<el-input-number controls-position="right" style="width:200px;" autoComplete="off" v-model="form.start_loucen" clearable :min="0" placeholder="请输入开始楼层"/>
					</el-form-item>
				</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="结束楼层" prop="end_loucen">
							<el-input-number controls-position="right" style="width:200px;" autoComplete="off" v-model="form.end_loucen" clearable :min="0" placeholder="请输入结束楼层"/>
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
				fydy_id:'',
				fybz_id:'',
				fwlx_id:'',
				fydy_id:'',
				fybz_id:'',
				zheng_fu: 1,
				start_loucen:'',
				end_loucen:'',
			},
			louyu_ids:[],
			fcxx_ids:[],
			fwlx_ids:[],
			loading:false,
			rules: {
				louyu_id:[
					{required: true, message: '楼宇/单元不能为空', trigger: 'blur'},
				],
				fwlx_id:[
					{required: true, message: '房屋类型不能为空', trigger: 'blur'},
				],
				zheng_fu:[
					{required: true, message: '正负楼层不能为空', trigger: 'blur'},
				],
				start_loucen:[
					{required: true, message: '开始楼层不能为空', trigger: 'blur'},
				],
				end_loucen:[
					{required: true, message: '结束楼层不能为空', trigger: 'blur'},
				],
			}
		}
	},
	watch:{
		show(val){
			if(val){
				axios.post(base_url + '/Fyfp/getLouyuid').then(res => {
					if(res.data.status == 200){
						this.louyu_ids = res.data.data.louyu_ids
						this.fwlx_ids = res.data.data.fwlx_ids
					}
				})
			}
		}
	},
	methods: {
		open(){
			const myURL = new URL(window.location.href)
			const urlobj = param2Obj(myURL.href)
			this.form.fydy_id = urlobj.fydy_id
			this.form.fybz_id = urlobj.fybz_id
		},
		selectFcxx_id(val){
			this.form.fcxx_id = ''
			axios.post(base_url + '/Fyfp/getFcxx_id',{louyu_id:val}).then(res => {
				if(res.data.status == 200){
					this.fcxx_ids = res.data.data
				}
			})
		},
		submit(){
			this.$refs['form'].validate(valid => {
				if(valid) {
					this.loading = true
					axios.post(base_url + '/Fyfp/plAdd',this.form).then(res => {
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
